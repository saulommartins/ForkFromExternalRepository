<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de regra de negócio para RPessoalPensao
    * Data de Criação: 05/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Regra de Negócio

    * Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php"                                             );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php"                                           );

class RPessoalPensao
{
   /**
      * @access Private
      * @var Object
       ligação com a instancia da classe Dependente que criou a instancia desta classe pensão

   */
   public $obRPessoalDependente;

   /**
      * @access Private
      * @var Object
   */
   public $obTransacao;
   /**
      * @access Private
      * @var Integer
   */
   public $inCodPensao;
   /**
      * @access Private
      * @var Date
   */
   public $dtLimite;
   /**
      * @access Private
      * @var Date
   */
   public $dtInclusao;
   /**
      * @access Private
      * @var String
   */
   public $stTipoPensao;
   /**
      * @access Private
      * @var Erro
   */
   public $flPercentual;
   /**
      * @access Private
      * @var Erro
   */
   public $flValor;
   /**
      * @access Private
      * @var String
   */
   public $stObservacao;
   /**
      * @access Private
      * @var String
   */
   public $stContaCorrente;
   /**
      * @access Private
      * @var Integer
   */
   public $inIncidencia;

   /***
       * @access public
       * @object
    */
   public $obRMONBanco;

   public $obRMONAgencia;

   public $arIncidencias = array();

   public $stTimeStamp;

   public $inCodBanco;
   public $inCodAgencia;

   public $codBiblioteca;
   public $codModulo;
   public $codFuncao;

   public $NumCGMResponsavel;

   public function setNumCGMResponsavel($valor) { return $this->NumCGMResponsavel = $valor ; }

   public function setCodBiblioteca($valor) { $this->codBiblioteca = $valor ;            }
   public function setCodModulo($valor) { $this->codModulo = $valor;                 }
   public function setCodFuncao($valor) { $this->codFuncao = $valor;                 }

   public function setCodAgencia($valor) { $this->inCodAgencia = $valor ;             }
   public function setCodbanco($valor) { $this->inCodBanco   = $valor ;             }

   public function setTimeStamp($valor) { $this->stTimeStamp = $valor ;              }
   public function addIncidencia($valor) { $this->arIncidencias[] = $valor; }
   public function setNOMBanco($objeto) { $this->obRMONBanco = $objeto ;             }
   public function setNOMAgencia($objeto) { $this->obRMONAgencia = $objeto;            }

   /**
       * @access Public
       * @param Object $valor
   */
   public function setTransacao($valor) { $this->obTransacao      = $valor; }
   /**
       * @access Public
       * @param Integer $valor
   */
   public function setCodPensao($valor) { $this->inCodPensao      = $valor; }
   /**
       * @access Public
       * @param Date $valor
   */
   public function setLimite($valor) { $this->dtLimite         = $valor; }
   /**
       * @access Public
       * @param Date $valor
   */
   public function setInclusao($valor) { $this->dtInclusao       = $valor; }
   /**
       * @access Public
       * @param String $valor
   */
   public function setTipoPensao($valor) { $this->stTipoPensao     = $valor; }
   /**
       * @access Public
       * @param Erro $valor
   */
   public function setPercentual($valor) { $this->flPercentual     = $valor; }
   /**
       * @access Public
       * @param Erro $valor
   */
   public function setValor($valor) { $this->flValor          = $valor; }
   /**
       * @access Public
       * @param String $valor
   */
   public function setObservacao($valor) { $this->stObservacao     = $valor; }
   /**
       * @access Public
       * @param String $valor
   */
   public function setContaCorrente($valor) { $this->stContaCorrente  = $valor; }
   /**
       * @access Public
       * @param Integer $valor
   */
   public function setIncidencia($valor) { $this->inIncidencia     = $valor; }

   /**
       * @access Public
       * @param Integer $valor
   */

   public function setRPessoalDependente(&$objeto) { $this->obRPessoalDependente = $objeto; }

   public function getCodAgencia() { return $this->inCodAgencia ; }

   public function getCodbanco() { return $this->inCodBanco   ; }

   public function getTimeStamp() { return $this->stTimeStamp;    }

   public function getNOMBanco() { return $this->obRMONBanco   ; }

   public function getNOMAgencia() { return $this->obRMONAgenciao; }

   public function getCodBiblioteca() { return $this->codBiblioteca ;}

   public function getCodModulo() { return $this->codModulo ;    }

   public function getCodFuncao() { return $this->codFuncao ;    }

   public function getNumCGMResponsavel() { return $this->NumCGMResponsavel;}

   /**
       * @access Public
       * @return Object
   */
   public function getTransacao() { return $this->obTransacao;      }
   /**
       * @access Public
       * @return Integer
   */
   public function getCodPensao() { return $this->inCodPensao;      }
   /**
       * @access Public
       * @return Date
   */
   public function getLimite() { return $this->dtLimite;         }
   /**
       * @access Public
       * @return Date
   */
   public function getInclusao() { return $this->dtInclusao;       }
   /**
       * @access Public
       * @return String
   */
   public function getTipoPensao() { return $this->stTipoPensao;     }
   /**
       * @access Public
       * @return Erro
   */
   public function getPercentual() { return $this->flPercentual;     }
   /**
       * @access Public
       * @return Erro
   */
   public function getValor() { return $this->flValor;          }
   /**
       * @access Public
       * @return String
   */
   public function getObservacao() { return $this->stObservacao;     }
   /**
       * @access Public
       * @return String
   */
   public function getContaCorrente() { return $this->stContaCorrente;  }
   /**
       * @access Public
       * @return Integer
   */
   public function getIncidencia() { return $this->inIncidencia;     }

   /**
        * Método construtor
        * @access Private
   */
   public function RPessoalPensao()
   {
       $this->setTransacao  ( new Transacao   );
       $this->setNOMBanco   ( new RMONBanco   );
       $this->setNOMAgencia ( new RMONAgencia );
       $this->obTransacao         = new Transacao;

   }
   /**
       * salvar
       * @access Public

       para incluir um novo registro a propriedade codPensao deve estar vazia ai sera criado um novo codigo
       para alterar basta colocar o numero do registro que será alterado nesta propriedade

   */
   public function salvar($boTransacao="")
   {
       include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensao.class.php");
       $obTPensao = new TPessoalPensao;
       $boFlagTransacao = false;

       $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

       $i = 0;
       $tmNow = '';
       $inCodSefip = '';

       // setando o time stamp que será usado
       $obTPensao->recuperaNow3( $stNow, $boTransacao );
       $this->setTimeStamp ( $stNow );

       // descobrindo o código, se for uma inclusão o codigo está vazio e será criado um novo,
       // se for alteração o codigo tera sido setado pela rotina que instanciou a classe.

       if (!$this->getCodPensao()) {
           // inclusão
           $stcampo = $obTPensao->getComplementoChave();
           $obTPensao->setComplementoChave ('');
           //$obTPensao->set
           $obTPensao->proximoCod( $inCodSefip , $boTransacao );
           $this->setCodPensao ( $inCodSefip );
           $obTPensao->setComplementoChave ( $stcampo );
       }
       if (!$obErro->ocorreu()) {
           // inclusão na tabela pessoal.pensao
           $obTPensao->setDado ( 'cod_pensao',     $this->getCodPensao()                                            );
           $obTPensao->setDado ( 'timestamp',      $this->getTimeStamp()                                            );
           $obTPensao->setDado ( 'cod_dependente', $this->obRPessoalDependente->getCodDependente()                  );
           $obTPensao->setDado ( 'cod_servidor',   $this->obRPessoalDependente->roPessoalServidor->getCodServidor() );
           $obTPensao->setDado ( 'tipo_pensao',    $this->getTipoPensao()                                           );
           $obTPensao->setDado ( 'dt_inclusao',    $this->getInclusao()                                             );
           $obTPensao->setDado ( 'dt_limite',      $this->getLimite ()                                              );
           $obTPensao->setDado ( 'percentual',     $this->getPercentual()                                           );
           $obTPensao->setDado ( 'observacao',     $this->getObservacao()                                           );
           $obErro = $obTPensao->inclusao ( $boTransacao );

       }

       if ( !$obErro->ocorreu()) {

           include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensaoBanco.class.php");
           // inclusao na tabela pessoal.pensao_banco
           $obTPensaobanco = new TPessoalPensaoBanco;
           $obTPensaobanco->setDado ('cod_pensao',     $this->getCodPensao ()    );
           $obTPensaobanco->setDado ('timestamp',      $this->getTimeStamp ()    );

           $obTPensaobanco->setDado ('cod_agencia',    $this->getCodAgencia ()   );
           $obTPensaobanco->setDado ('cod_banco',      $this->getCodbanco ()     );
           $obTPensaobanco->setDado ('conta_corrente', $this->getContaCorrente() );
           $obErro = $obTPensaobanco->inclusao ( $boTransacao );
       }
       if ( !$obErro->ocorreu() ) {
          if ( $this->getValor()) {

               include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensaoValor.class.php");
               // inclusão na tabela pessoal.pensao.valor
               $obTPensaoValor = new TPessoalPensaoValor;
               $obTPensaoValor->setDado('cod_pensao' ,$this->getCodPensao() );
               $obTPensaoValor->setDado('timestamp'  ,$this->getTimeStamp() );
               $obTPensaoValor->setDado('valor'      ,$this->getValor()     );
               $obErro = $obTPensaoValor->inclusao ( $boTransacao );
          } else {
               include_once(CAM_GRH_PES_MAPEAMENTO.'TPessoalPensaoFuncao.class.php');
               /// inclusão na tabela pessoal.Pensao_Funcao
               $obTPensaoFuncao = new TPessoalPensaoFuncao;
               $obTPensaoFuncao->setDado ('cod_pensao'     , $this->getCodPensao()     );
               $obTPensaoFuncao->setDado ('timestamp'      , $this->getTimeStamp()     );
               $obTPensaoFuncao->setDado ('cod_biblioteca' , $this->getCodBiblioteca() );
               $obTPensaoFuncao->setDado ('cod_modulo'     , $this->getCodModulo()     );
               $obTPensaoFuncao->setDado ('cod_funcao'     , $this->getCodFuncao()     );
               $obErro = $obTPensaoFuncao->inclusao ( $boTransacao );
          }
       }

       if ( ( !$obErro->ocorreu() ) and ($this->getNumCGMResponsavel () ) ) {
           include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalResponsavelLegal.class.php");
          // iclusão na tabela pessoal.responsavel_legal
          $obTResponsavelLegal = new TPessoalResponsavelLegal;
          $obTResponsavelLegal->setDado ('cod_pensao' , $this->getCodPensao() );
          $obTResponsavelLegal->setDado ('timestamp'  , $this->getTimeStamp() );
          $obTResponsavelLegal->setDado ('numcgm'     , $this->getNumCGMResponsavel ()    );
          $obErro = $obTResponsavelLegal->inclusao ( $boTransacao );
       }

       if ( ( count ( $this->arIncidencias ) > 0 ) and ( !$obErro->ocorreu() ) ) {
           include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensaoIncidencia.class.php");
           $obTPessoalPensaoIncidencia = new TPessoalPensaoIncidencia;

           $i = 0;
           while ( ($i< count( $this->arIncidencias))and (!$obErro->ocorreu()) ) {
               $obTPessoalPensaoIncidencia->setDado ('cod_incidencia' ,$this->arIncidencias[$i] );
               $obTPessoalPensaoIncidencia->setDado ('cod_pensao'     ,$this->getCodPensao()    );
               $obTPessoalPensaoIncidencia->setDado ('timestamp'      ,$this->getTimeStamp()    );
               $obErro = $obTPessoalPensaoIncidencia->inclusao ( $boTransacao );
               $i++;
           }
       }

       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPensao );

       return $obErro;
   }

   /**
       * Alterar
       * @access Public
   */
   public function alterar($boTransacao="")
   {
       include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensao.class.php");
       $obTPensao = new TPessoalPensao;
       $boFlagTransacao = false;
       $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
       if (!$obErro->ocorreu()) {

       }
       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPensao );

       return $obErro;
   }

   /**
       * Excluir
       * @access Public
   */
   public function excluir($boTransacao="")
   {
       include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensaoExcluida.class.php");
       $obTPessoalPensaoExcluida = new TPessoalPensaoExcluida;
       $boFlagTransacao = false;

       $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
       if (!$obErro->ocorreu()) {
           $obTPessoalPensaoExcluida->setDado ('cod_pensao', $this->getCodPensao() );
           $obTPessoalPensaoExcluida->setDado ('timestamp' , $this->getTimeStamp() );
           $obErro = $obTPessoalPensaoExcluida->inclusao();
       }
       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPensao );

       return $obErro;
   }

   /**
       * Método listar
       * @access Private
   */
   public function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
   {
       include_once(CAM_GRH_PES_MAPEAMENTO.'TPessoalPensao.class.php');
       $obTPensao = new TPessoalPensao;

       if ($stFiltro) {
           // retirando o AND do inicio do filtro se ouver um
           $stFiltro = trim ($stFiltro);

           if ( strtoupper(substr($stFiltro,0,4)) == "AND ") {
               $stFiltro = ' WHERE '.substr($stFiltro,4);
           }
       }
       $obErro = $obTPensao->recuperaRelacionamento ($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

       return $obErro;
   }

   public function listarPensaoPorCodigoServidor($inCodigo)
   {
       $stFiltro = " and cod_pensao = ".$inCodigo;

       return $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   public function listarFiltro(&$rsRecordSet, $arFiltros, $boTransacao = '')
   {
       $stFiltro = '';

       foreach ($arFiltros as $arFiltro) {
           $stFiltro .= ' AND '.$arFiltro['campo'].' '. $arFiltro['condicao'].' ' .$arFiltro['valor'];
       }

       return $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   /**
       * Método listarIncidencias
       * @access Public
       * Return obErro
   */
   public function listarIncidencias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
   {
       include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalIncidencia.class.php");
       $obTPessoalIncidencia = new TPessoalIncidencia;
       $stOrder .= ' cod_incidencia ';
       $obErro = $obTPessoalIncidencia->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

       return $obErro;
   }//function listarIncidencias ( &$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")

   public function listarIncidenciasPensao(&$rsRecordSet, $inCodPensao, $stTimeStamp)
   {
       include_once(CAM_GRH_PES_MAPEAMENTO.'TPessoalPensaoIncidencia.class.php');
       $obTPessoalPensaoIncidencia = new TPessoalPensaoIncidencia;
       $stFiltro = '';

       $stFiltro = " where cod_pensao = ".$inCodPensao." and timestamp = '".$stTimeStamp."' ";

       $obTPessoalPensaoIncidencia->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

   }//function listarIncidenciasPensao ( $inCodDep )

}

?>
