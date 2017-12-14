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
* Classe de regra de negócio para RBeneficioGrupoConcessao
* Data de Criação: 20/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Id: RBeneficioGrupoConcessao.class.php 65736 2016-06-10 20:18:11Z michel $

* Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioGrupoConcessao.class.php";
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioGrupoConcessaoValeTransporte.class.php";

class RBeneficioGrupoConcessao
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
var $obTBeneficioGrupoConcessao;
/**
   * @access Private
   * @var Array
*/
var $arRBeneficioConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioConcessaoValeTransporte;
/**
   * @access Private
   * @var Integer
*/
var $inCodGrupo;
/**
   * @access Private
   * @var String
*/
var $stDescricao;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioGrupoConcessao($valor) { $this->obTBeneficioGrupoConcessao           = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRBeneficioConcessaoValeTransporte($valor) { $this->arRBeneficioConcessaoValeTransporte  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioConcessaoValeTransporte($valor) { $this->roRBeneficioConcessaoValeTransporte  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodGrupo($valor) { $this->inCodGrupo                           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao                          = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                          }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioGrupoConcessao() { return $this->obTBeneficioGrupoConcessao;           }
/**
    * @access Public
    * @return Array
*/
function getARRBeneficioConcessaoValeTransporte() { return $this->arRBeneficioConcessaoValeTransporte;  }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioConcessaoValeTransporte() { return $this->roRBeneficioConcessaoValeTransporte;  }
/**
    * @access Public
    * @return Integer
*/
function getCodGrupo() { return $this->inCodGrupo;                           }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                          }

/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    $this->setTransacao                                   ( new Transacao                   );
    $this->setTBeneficioGrupoConcessao                    ( new TBeneficioGrupoConcessao    );
    $this->setARRBeneficioConcessaoValeTransporte         ( array()                         );
}

/**
    * Inclusão
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function incluirGrupoConcessao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stCampoCod = $this->obTBeneficioGrupoConcessao->getCampoCod();
        $stComplementoChave = $this->obTBeneficioGrupoConcessao->getComplementoChave();
        $this->obTBeneficioGrupoConcessao->setCampoCod( "cod_grupo" );
        $this->obTBeneficioGrupoConcessao->setComplementoChave( "" );
        $obErro = $this->obTBeneficioGrupoConcessao->proximoCod( $inCodGrupo, $boTransacao );
        $this->obTBeneficioGrupoConcessao->setCampoCod( $inCampoCod );
        $this->obTBeneficioGrupoConcessao->setComplementoChave( $stComplementoChave );
        $this->setCodGrupo( $inCodGrupo );
        $this->obTBeneficioGrupoConcessao->setDado('cod_grupo', $this->getCodGrupo()    );
        $this->obTBeneficioGrupoConcessao->setDado('descricao', $this->getDescricao()   );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTBeneficioGrupoConcessao->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporte);$inIndex++) {
                $obRBeneficioConcessaoValeTransporte = &$this->arRBeneficioConcessaoValeTransporte[$inIndex];
                $obErro = $obRBeneficioConcessaoValeTransporte->incluirConcessaoValeTransporte($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioGrupoConcessao );

    return $obErro;
}

/**
    * Alteração
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function alterarGrupoConcessao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioGrupoConcessao->setDado('cod_grupo', $this->getCodGrupo()    );
        $this->obTBeneficioGrupoConcessao->setDado('descricao', $this->getDescricao()   );
        $obErro = $this->obTBeneficioGrupoConcessao->alteracao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporte);$inIndex++) {
                $obRBeneficioConcessaoValeTransporte = &$this->arRBeneficioConcessaoValeTransporte[$inIndex];
                if ( $obRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
                    $obErro = $obRBeneficioConcessaoValeTransporte->alterarConcessaoValeTransporte($boTransacao);
                } else {
                    $obErro = $obRBeneficioConcessaoValeTransporte->incluirConcessaoValeTransporte($boTransacao);
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioGrupoConcessao );

    return $obErro;
}

/**
    * Exclusão
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function excluirGrupoConcessao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRBeneficioConcessaoValeTransporte->listarConcessoesCadastradasPorGrupo($rsGrupoConcessao,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->roRBeneficioConcessaoValeTransporte->excluirConcessaoValeTransporte($boTransacao);
        }
        if ( $rsGrupoConcessao->getNumLinhas() == 1 and !$obErro->ocorreu() ) {
            $this->obTBeneficioGrupoConcessao->setDado('cod_grupo', $this->getCodGrupo()    );
            $obErro = $this->obTBeneficioGrupoConcessao->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioGrupoConcessao );

    return $obErro;
}
/**
    * Adiciona um RBeneficioConcessaoValeTransporte ao array de referencia-objeto
    * @access Public
*/
function addRBeneficioConcessaoValeTransporte()
{
     $this->arRBeneficioConcessaoValeTransporte[] = new RBeneficioConcessaoValeTransporte ();
     $this->roRBeneficioConcessaoValeTransporte   = &$this->arRBeneficioConcessaoValeTransporte[ count($this->arRBeneficioConcessaoValeTransporte) - 1 ];
     $this->roRBeneficioConcessaoValeTransporte->setRORBeneficioGrupoConcessao( $this );
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro = $this->obTBeneficioGrupoConcessao->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarGrupoConcessao
    * $access Public
*/
function listarGrupoConcessao(&$rsRecordSet,$boTransacao="")
{
    if ( $this->getCodGrupo() ) {
        $stFiltro .= " AND cod_grupo = ". $this->getCodGrupo();
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " AND descricao = ". $this->getDescricao();
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $stOrder  = "descricao";
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarGrupoConcessaoSituacao
    * Lista as concessoes de um determinado grupo, de acordo com sua situacao (inicializado ou nao)
    * @access Public
*/
function listarGrupoConcessaoSituacao(&$rsRecordSet,$boTransacao="")
{
    if ($inCodGrupo = $this->getCodGrupo() )
        $stFiltro .= " AND Bgcvt.cod_grupo = ".$inCodGrupo;
    if ($stInicializacao = $this->roRBeneficioConcessaoValeTransporte->getInicializacao() )
        $stFiltro .= " AND Bcvt.inicializado = '".$stInicializacao."' ";
    if ($inCodMes = $this->roRBeneficioConcessaoValeTransporte->getCodMes() )
        $stFiltro .= " AND Bcvt.cod_mes = ".$inCodMes." ";
    if ($stExercicio = $this->roRBeneficioConcessaoValeTransporte->getExercicio() )
        $stFiltro .= " AND Bcvt.exercicio = '".$stExercicio."' ";
    $obTBeneficioGrupoConcessaoValeTransporte = new TBeneficioGrupoConcessaoValeTransporte;
    $obErro = $obTBeneficioGrupoConcessaoValeTransporte->recuperaGrupoConcessaoValeTransporteSituacao($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Lista a concessão vigente parar fazer a inicialização de vale-transporte
    * @access Public
*/
function listarGrupoConcessaoVigenciaAtual(&$rsRecordSet, $boTransacao="")
{
    $obTBeneficioGrupoConcessaoValeTransporte = new TBeneficioGrupoConcessaoValeTransporte;
    $obErro = new Erro;
    if ($inCodConcessao = $this->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
        $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_concessao',$inCodConcessao);
        $obErro = $obTBeneficioGrupoConcessaoValeTransporte->recuperaGrupoConcessaoVigenciaAtual($rsRecordSet,'','',$boTransacao);
    }

    return $obErro;
}

/**
  * Lista todos grupos que tem uma concessão
  * @access Public
 **/
function listarGrupos(&$rsRecordSet,$boTransacao="")
{
    $obTBeneficioGrupoConcessaoValeTransporte = new TBeneficioGrupoConcessaoValeTransporte;
    $obErro = $obTBeneficioGrupoConcessaoValeTransporte->recuperaGrupoConcessao($rsRecordSet,'','',$boTransacao);

    return $obErro;
}

/**
    * Método listarGrupoConcessaoValeTransporte
    * @access Public
*/
function listarGrupoConcessaoValeTransporte(&$rsRecordSet,$boTransacao="")
{
    $obTBeneficioGrupoConcessaoValeTransporte = new TBeneficioGrupoConcessaoValeTransporte;
    if ( $this->getCodGrupo() ) {
        $stFiltro .= " AND cod_grupo = ".$this->getCodGrupo();
    }
    if ( is_object( $this->roRBeneficioConcessaoValeTransporte ) ) {
        if ( $this->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
            $stFiltro .= " AND cod_mes = ".$this->roRBeneficioConcessaoValeTransporte->getCodMes();
        }
        if ( $this->roRBeneficioConcessaoValeTransporte->getExercicio() ) {
            $stFiltro .= " AND exercicio = '".$this->roRBeneficioConcessaoValeTransporte->getExercicio()."'";
        }
        if ( $this->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
            $stFiltro .= " AND cod_concessao = ".$this->roRBeneficioConcessaoValeTransporte->getCodConcessao();
        }
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $obTBeneficioGrupoConcessaoValeTransporte->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Inicializa vale-transporte de um grupo
    * @access Public
*/
function inicializarConcessaoValeTransporte($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu() && $this->getCodGrupo()) {
        //Lista as concessoes (nao-inicializadas) de um determinado grupo
        $this->roRBeneficioConcessaoValeTransporte->setInicializacao('f');
        $inCodMes = $this->roRBeneficioConcessaoValeTransporte->getCodMes();
        $stExercicio = $this->roRBeneficioConcessaoValeTransporte->getExercicio();
        $this->roRBeneficioConcessaoValeTransporte->setCodMes('');
        $this->roRBeneficioConcessaoValeTransporte->setExercicio('');
        $obErro = $this->listarGrupoConcessaoSituacao($rsConcessao,$boTransacao);
        $this->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
        $this->roRBeneficioConcessaoValeTransporte->setExercicio($stExercicio);
        if (!$obErro->ocorreu() && $rsConcessao->getNumLinhas() > 0) {
            while ( !$rsConcessao->eof() ) {
                $this->roRBeneficioConcessaoValeTransporte->setCodConcessao($rsConcessao->getCampo('cod_concessao'));
                //Verifica se o mes/exercicio a ser inicializado ja esta inserido como concessao
                $obErro = $this->listarGrupoConcessaoValeTransporte($rsVerificaConcessao,$boTransacao);
                if (!$obErro->ocorreu()) {
                    if ( ($rsVerificaConcessao->getCampo('exercicio') != $this->roRBeneficioConcessaoValeTransporte->getExercicio()) ||
                         ($rsVerificaConcessao->getCampo('cod_mes') ) != $this->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
                        //Consulta a vigencia atual para cada concessao
                        $obErro = $this->listarGrupoConcessaoVigenciaAtual($rsVigenciaAtual,$boTransacao);
                        if (!$obErro->ocorreu()) {
                            $obErro = $this->roRBeneficioConcessaoValeTransporte->incluirInicializacaoValeTransporte($rsVigenciaAtual,$boTransacao);
                        }
                    }
                }
                $rsConcessao->proximo();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte);

    return $obErro;
}

/**
    * Monta a lista para exclusao da inicializacao de vale-transporte de um grupo
    * @access Public
*/
function listarConcessaoValeTransporteInicializados(&$rsRecordSet,$boTransacao="")
{
    $rsRecordSet = new RecordSet;
    //Lista os tipos de vale-transporte
    $this->roRBeneficioConcessaoValeTransporte->listarTipo($rsTipo,$boTransacao);
    $arTipo = $rsTipo->getElementos();
    if ($this->getCodGrupo()) {
        //Lista as concessoes inicializadas de um determinado grupo
        $this->roRBeneficioConcessaoValeTransporte->setInicializacao('t');
        $obErro = $this->listarGrupoConcessaoSituacao($rsConcessao,$boTransacao);
        if (!$obErro->ocorreu() && $rsConcessao->getNumLinhas() > 0) {
            while ( !$rsConcessao->eof() ) {
                //$this->roRBeneficioConcessaoValeTransporte->setCodConcessao($rsConcessao->getCampo('cod_concessao'));
                //Consulta os dados da concessao
                $stFiltro  = " WHERE Bcvt.cod_concessao = ".$rsConcessao->getCampo('cod_concessao')."  \n";
                $stFiltro .= "   AND Bcvt.exercicio = '".$rsConcessao->getCampo('exercicio')."'        \n";
                $stFiltro .= "   AND Bcvt.cod_mes = ".$rsConcessao->getCampo('cod_mes')."              \n";
                $obErro = $this->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporteInicializar($rsInicializacao, $stFiltro, "", $boTransacao);
                if (!$obErro->ocorreu()) {
                    $this->listarGrupoConcessao($rsGrupo,$boTransacao);
                    $arTemp[] = array (
                        'grupo'      => trim($rsGrupo->getCampo('descricao')) ,
                        'cod_grupo'  => $this->getCodGrupo() ,
                        'concessao'  => $rsConcessao->getCampo('cod_concessao') ,
                        'tipo'       => $arTipo[$rsInicializacao->getCampo('cod_tipo')-1]['descricao'] ,
                        'mes'        => $rsInicializacao->getCampo('cod_mes') ,
                        'ano'        => $rsInicializacao->getCampo('exercicio') ,
                        'quantidade' => $rsInicializacao->getCampo('quantidade')
                         );
                }
                $rsConcessao->proximo();
            }
        }
    }
    if (count($arTemp))
        $rsRecordSet->preenche($arTemp);

    return $obErro;
}

/**
    * Faz a exclusao de inicialização de vale-transporte
    * @access Public
    * @param Object $boTransacao Transação
*/
function excluirAssociacao($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        if ($this->getCodGrupo()) {
            $obTBeneficioGrupoConcessaoValeTransporte = new TBeneficioGrupoConcessaoValeTransporte;
            foreach ($this->arRBeneficioConcessaoValeTransporte as $obRBeneficioConcessaoValeTransporte) {
                $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_grupo'    ,$this->getCodGrupo());
                $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_concessao',$obRBeneficioConcessaoValeTransporte->getCodConcessao());
                $obTBeneficioGrupoConcessaoValeTransporte->setDado('exercicio'    ,$obRBeneficioConcessaoValeTransporte->getExercicio());
                $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_mes'      ,$obRBeneficioConcessaoValeTransporte->getCodMes());
                $obErro = $obTBeneficioGrupoConcessaoValeTransporte->exclusao($boTransacao);
                if (!$obErro->ocorreu()) {
                    $obErro = $obRBeneficioConcessaoValeTransporte->excluirAssociacao($boTransacao);
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTBeneficioGrupoConcessaoValeTransporte );

    return $obErro;
}

}
?>
