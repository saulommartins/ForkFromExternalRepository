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
    * Classe de Regra de Almoxarifado
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.07
*/

/*
$Log$
Revision 1.13  2007/01/15 10:49:42  hboaventura
Bug #8058#

Revision 1.12  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.11  2006/07/06 12:09:31  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );

/**
    * Classe de Regra de Almoxarifado
    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoCentroDeCustos
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
var $arDotacaoes;
/**
   * @access Private
   * @var Object
*/
var $arEntidades;

/**
    * @access Private
    * @var Integer
*/
var $inCodigo;

/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Private
    * @var Date
*/
var $dtVigencia;

/**
  * @access Private
  * @var Object
*/
var $roRPermissaCentroDeCustos;

/**
    * @access Public
    * @return Integer
*/
function setCodigo($value) { $this->inCodigo = $value; }

/**
    * @access Public
    * @return Integer
*/
function getCodigo() { return $this->inCodigo; }

/**
    * @access Public
    * @return Integer
*/
function setDescricao($value) { $this->stDescricao = $value; }

/**
    * @access Public
    * @return Integer
*/
function getDescricao() { return $this->stDescricao; }
/**
    * @access Public
    * @return Integer
*/
function setVigencia($value) { $this->dtVigencia = $value; }

/**
    * @access Public
    * @return Integer
*/
function getVigencia() { return $this->dtVigencia; }

/**
     * Método construtor
     * @access Public
*/

function RAlmoxarifadoCentroDeCustos()
{
    include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php");
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php");
    $this->obTransacao           = new Transacao ;
    $this->obRCGMResponsavel     = new RCGMPessoaFisica();
    $this->addEntidade();
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPermissaoUsuario(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{

    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto();

    if ($this->arEntidades) {
        if ($this->getCodigo()) {
            $stFiltro  .= " centro_custo_permissao.cod_centro = "  . $this->getCodigo() . "  AND ";
        }

       $arEntidades = array();

       foreach ($this->arEntidades as $obEntidade) {
           if ($obEntidade->getCodigoEntidade())
              $arEntidades[] = $obEntidade->getCodigoEntidade();
       }
       if ($arEntidades) {
          $stEntidades = implode(',',$arEntidades);
          $stFiltro  .= " centro_custo_entidade.cod_entidade in ("  . $stEntidades . ")  AND ";
       }
    }
    if ($this->getDescricao()) {
        $stFiltro  .= " descricao ilike '"  . $this->getDescricao() . "'  AND ";
    }
    $stFiltro .= " centro_custo_permissao.numcgm = "  . Sessao::read('numCgm') . " AND " ;
    $stFiltro = ($stFiltro) ? " AND  " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTAlmoxarifadoCentroCusto->recuperaPermissaoUsuario( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto();
    $stFiltro = "";
    $boTransacao = "";
    if ($this->getCodigo()) {
        $stFiltro  .= " cod_centro = "  . $this->getCodigo() . "  AND ";
    }
    if ($this->arEntidades) {
       $arEntidades = array();

       foreach ($this->arEntidades as $obEntidade) {
           if ($obEntidade->getCodigoEntidade())
              $arEntidades[] = $obEntidade->getCodigoEntidade();
       }
       if ($arEntidades) {
          $stEntidades = implode(',',$arEntidades);
          $stFiltro  .= " centro_custo_entidade.cod_entidade in ("  . $stEntidades . ")  AND ";
       }
    }
    if ($this->getDescricao()) {
        $stFiltro  .= " descricao ilike '"  . $this->getDescricao() . "'  AND ";
    }

    $stFiltro .= " centro_custo_permissao.responsavel = TRUE AND ";

    $stFiltro = ($stFiltro) ? " AND  " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";    
    $obErro = $obTAlmoxarifadoCentroCusto->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Adiciona uma Entidade ao Centro de Custo
    * @access Public
*/
function addEntidade()
{
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php");
    $this->arEntidades[] = new ROrcamentoEntidade();
    $this->roUltimaEntidade = &$this->arEntidades[ count($this->arEntidades) - 1];
}

/**
    * Adiciona uma Dotação ao Centro de Custo
    * @access Public
*/
function addDotacao()
{
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php");
    $this->arDotacoes[] = new ROrcamentoDespesa();
    $this->roUltimaDotacao = &$this->arDotacoes[ count($this->arDotacoes) - 1];
}

/**
    * Lista dotações do Centro de Custos
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDotacoes(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
   include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoDotacao.class.php");
   $obTAlmoxarifadoCentroCustoDotacao = new TAlmoxarifadoCentroCustoDotacao();
   $stFiltro = "";
   if ($this->getCodigo()) {
      $stFiltro .= ' and cod_centro = '. $this->getCodigo();
   }
   if ($this->roUltimaEntidade->getCodigoEntidade()) {
      $stFiltro .= ' and cod_entidade = '. $this->roUltimaEntidade->getCodigoEntidade();
   }

   $stFiltro .= " AND accd.exercicio = '".Sessao::getExercicio()."' ";

   $obTAlmoxarifadoCentroCustoDotacao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao);   
   
}

/**
    * Lista dotações do Centro de Custos
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValidaDescricao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
   include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
   $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto();
   $stFiltro = "";
   if ($this->getDescricao()) {
      $stFiltro .= " where upper(descricao) = upper('". $this->getDescricao()."')";
   }

   return $obTAlmoxarifadoCentroCusto->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);
}

/**
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVigentes(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto();
    $stFiltro = "";
    if ($this->getCodigo()) {
        $stFiltro  .= " cod_entidade= "  . $this->inCodigo . "  AND ";
    }
    $stFiltro .= " dt_vigencia= " ;
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_centro";

    $obErro = $obTAlmoxarifadoCentroCusto->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Incluir Centro de Custo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function incluir($boTransacao = "")
{
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php");
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoDotacao.class.php");
    include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php");

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto;
    $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
    $obRAlmoxarifadoPermissaoCentroDeCustos = new RAlmoxarifadoPermissaoCentroDeCustos;
    $obTAlmoxarifadoCentroCustoDotacao = new TAlmoxarifadoCentroCustoDotacao;
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaDescricao( $boValida,$stAcao='incluir',$boTransacao);
        if ($boValida == 'FALSE') {
            $obErro->setDescricao('Esta Centro de Custo já está cadastrado.');
        } else {
           $obErro = $obTAlmoxarifadoCentroCusto->proximoCod( $this->inCodigo, $boTransacao );

           if ( !$obErro->ocorreu() ) {
               $obTAlmoxarifadoCentroCusto->setDado( "cod_centro" , $this->getCodigo() );
               $obTAlmoxarifadoCentroCusto->setDado( "descricao"   , $this->getDescricao() );
               $obTAlmoxarifadoCentroCusto->setDado( "dt_vigencia"  , $this->getVigencia() );
           }
           $obErro = $obTAlmoxarifadoCentroCusto->inclusao( $boTransacao );
           if ( !$obErro->ocorreu() ) {
               $obRAlmoxarifadoPermissaoCentroDeCustos->obRCGMPessoaFisica->setNumCGM($this->obRCGMResponsavel->getNumCGM());
               $obRAlmoxarifadoPermissaoCentroDeCustos->addCentroDeCustos();
               $obRAlmoxarifadoPermissaoCentroDeCustos->roUltimoCentro->setCodigo($this->getCodigo());

           }

           $obErro = $obRAlmoxarifadoPermissaoCentroDeCustos->salvarResponsavel( $boTransacao );

           if ( !$obErro->ocorreu () ) {
               $obTAlmoxarifadoCentroCustoEntidade->setDado( "cod_centro", $this->getCodigo());
               $obTAlmoxarifadoCentroCustoEntidade->setDado( "exercicio", $this->roUltimaEntidade->getExercicio());
               $obTAlmoxarifadoCentroCustoEntidade->setDado( "cod_entidade", $this->roUltimaEntidade->getCodigoEntidade() );
               $obErro = $obTAlmoxarifadoCentroCustoEntidade->inclusao( $boTransacao );

           }
           if ( !$obErro->ocorreu () ) {
               for ($inCount=0; $inCount<count($this->arDotacoes); $inCount++) {
                   $obDotacao = $this->arDotacoes[$inCount];

                   $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_entidade", $this->roUltimaEntidade->getCodigoEntidade());
                   $obTAlmoxarifadoCentroCustoDotacao->setDado( "exercicio", $this->roUltimaEntidade->getExercicio());
                   $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_centro", $this->getCodigo());
                   $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_despesa", $obDotacao->getCodDespesa());
                   $obErro = $obTAlmoxarifadoCentroCustoDotacao->inclusao( $boTransacao );
                   if ( $obErro->ocorreu() ) {
                        break;
                   }
               }
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoAlmoxarife );
    }

    return $obErro;
}

/**
    * Alterar Centro de Custo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php");
    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoDotacao.class.php");
    include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php");

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto;
    $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
    $obRAlmoxarifadoPermissaoCentroDeCustos = new RAlmoxarifadoPermissaoCentroDeCustos;
    $obTAlmoxarifadoCentroCustoDotacao = new TAlmoxarifadoCentroCustoDotacao;
    if ( !$obErro->ocorreu() ) {
        $obTAlmoxarifadoCentroCusto->setDado( "cod_centro" , $this->getCodigo() );
        $obTAlmoxarifadoCentroCusto->setDado( "descricao"   , $this->getDescricao() );
        $obTAlmoxarifadoCentroCusto->setDado( "dt_vigencia"  , $this->getVigencia() );
        $obErro = $obTAlmoxarifadoCentroCusto->alteracao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obRAlmoxarifadoPermissaoCentroDeCustos->addCentroDeCustos();
            $obRAlmoxarifadoPermissaoCentroDeCustos->roUltimoCentro->setCodigo($this->getCodigo());
            $obRAlmoxarifadoPermissaoCentroDeCustos->obRCGMPessoaFisica->setNumCGM($this->obRCGMResponsavel->getNumCGM());
            $obErro = $obRAlmoxarifadoPermissaoCentroDeCustos->excluir( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $obRAlmoxarifadoPermissaoCentroDeCustos->obRCGMPessoaFisica->setNumCGM($this->obRCGMResponsavel->getNumCGM());
            $obRAlmoxarifadoPermissaoCentroDeCustos->addCentroDeCustos();
            $obRAlmoxarifadoPermissaoCentroDeCustos->roUltimoCentro->setCodigo($this->getCodigo());
            $obErro = $obRAlmoxarifadoPermissaoCentroDeCustos->salvarResponsavel( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_centro" , $this->getCodigo() );
            $obTAlmoxarifadoCentroCustoDotacao->setDado( "exercicio"  , Sessao::getExercicio() );                
            $obErro = $obTAlmoxarifadoCentroCustoDotacao->exclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoCentroCustoEntidade->setDado( "cod_centro" , $this->getCodigo() );
            $obTAlmoxarifadoCentroCustoEntidade->setDado( "exercicio"  , Sessao::getExercicio() );
            $obErro = $obTAlmoxarifadoCentroCustoEntidade->exclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu () ) {
            $obTAlmoxarifadoCentroCustoEntidade->setDado( "cod_centro", $this->getCodigo());
            $obTAlmoxarifadoCentroCustoEntidade->setDado( "exercicio", $this->roUltimaEntidade->getExercicio());
            $obTAlmoxarifadoCentroCustoEntidade->setDado( "cod_entidade", $this->roUltimaEntidade->getCodigoEntidade() );
            $obErro = $obTAlmoxarifadoCentroCustoEntidade->inclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu () ) {
            for ($inCount=0; $inCount<count($this->arDotacoes); $inCount++) {
                $obDotacao = $this->arDotacoes[$inCount];

                $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_entidade", $this->roUltimaEntidade->getCodigoEntidade());
                $obTAlmoxarifadoCentroCustoDotacao->setDado( "exercicio", $this->roUltimaEntidade->getExercicio());
                $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_centro", $this->getCodigo());
                $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_despesa", $obDotacao->getCodDespesa());
                $obErro = $obTAlmoxarifadoCentroCustoDotacao->inclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                     break;
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoAlmoxarife );
    }

        return $obErro;
    }

    /**
        * Exclui Centro de Custo
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */

    public function excluir($boTransacao = "")
    {
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php");
        include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoDotacao.class.php");
        include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php");

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto;
        $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
        $obRAlmoxarifadoPermissaoCentroDeCustos = new RAlmoxarifadoPermissaoCentroDeCustos;
        $obTAlmoxarifadoCentroCustoDotacao = new TAlmoxarifadoCentroCustoDotacao;
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoCentroCustoDotacao->setDado( "cod_centro" , $this->getCodigo() );
            $obErro = $obTAlmoxarifadoCentroCustoDotacao->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTAlmoxarifadoCentroCustoEntidade->setDado( "cod_centro" , $this->getCodigo() );
            $obErro = $obTAlmoxarifadoCentroCustoEntidade->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {

            $obRAlmoxarifadoPermissaoCentroDeCustos->addCentroDeCustos();
            $obRAlmoxarifadoPermissaoCentroDeCustos->roUltimoCentro->setCodigo($this->getCodigo());

            $obErro = $obRAlmoxarifadoPermissaoCentroDeCustos->excluir( $boTransacao );

        }
        if ( !$obErro->ocorreu() ) {
            $obTAlmoxarifadoCentroCusto->setDado( "cod_centro" , $this->getCodigo() );
            $obErro = $obTAlmoxarifadoCentroCusto->exclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAlmoxarifadoCentroCusto);
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function consultar($boTransacao = "")
{

    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCusto.class.php");
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto;
    $obTAlmoxarifadoCentroCusto->setDado("cod_centro", $this->inCodigo);
    $obErro = $obTAlmoxarifadoCentroCusto->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->setDescricao($rsRecordSet->getCampo("descricao"));

        $this->listarDotacoes( $rsDotacoes, "", $boTransacao );

        while ( !$rsDotacoes->EOF() ) {
            $this->addDotacao();
            $this->roUltimaDotacao->setCodDespesa( $rsDotacoes->getCampo('cod_despesa'));
            $this->roUltimaDotacao->setDescricao ( $rsDotacoes->getCampo('descricao' ) );

            $rsDotacoes->proximo();
        }
    }

    return $obErro;
}

/**
  * Instancia um objeto da regra PermissaoCentroDeCustos
  * @access Public
  * @param Object $obRegra
*/
function addPermissaoCentroDeCustos()
{
    include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php");
    $obRegra = new RAlmoxarifadoPermissaoCentroDeCustos;
    $this->roRPermissaoCentroDeCustos = &$obRegra;
}

function validaDescricao(&$boValida ,$stAcao ,$boTransacao)
{
    $stOrder ='';
        $boValida = 'TRUE';
        $obErro = $this->listarValidaDescricao ( $rsLista,$stOrder,$boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsLista->getNumLinhas() > 0 ) {
                if ($stAcao == 'incluir') {
                    $boValida = 'FALSE';
                } else {
                    while (!$rsLista->eof()) {
                        if ($rsLista->getCampo('cod_marca') != $this->getCodigo()  ) {
                            $boValida = 'FALSE';
                        }
                        $rsLista->proximo();
                }
            }
        }
    }

    return $obErro;
}

}
