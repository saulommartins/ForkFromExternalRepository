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
    * Classe de Regra de Negócio para Bordero
    * Data de Criação   : 24/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

/*
$Log$
Revision 1.15  2007/04/30 19:21:10  cako
implementação uc-02.03.28

Revision 1.14  2007/03/30 21:59:12  cako
Bug #7884#

Revision 1.13  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS      ."Transacao.class.php"                         );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaTransacaoPagamento.class.php" );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaTransacaoTransferencia.class.php" );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaUsuarioTerminal.class.php"        );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaAssinatura.class.php"             );
include_once ( CAM_GF_ORC_NEGOCIO      ."ROrcamentoEntidade.class.php"                );
include_once ( CAM_GF_CONT_NEGOCIO     ."RContabilidadePlanoBanco.class.php"          );
include_once ( CAM_GF_TES_NEGOCIO      ."RTesourariaAutenticacao.class.php"           );

/**
    * Classe de Regra de Bordero
    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaBordero
{
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaAutenticacao;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaBoletim;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoBanco;
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaUsuarioTerminal;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/*
    * @var Object
    * @access Private
*/
var $inCodBordero;
/*
    * @var Integer
    * @access Private
*/
var $inCodBorderoInicial;
/*
    * @var Integer
    * @access Private
*/
var $inCodBorderoFinal;
/*
    * @var String
    * @access Private
*/
var $stExercicio;
/*
    * @var String
    * @access Private
*/
var $stTimestampBordero;
/*
    * @var String
    * @access Private
*/
var $stTimestampBorderoInicial;
/*
    * @var String
    * @access Private
*/
var $stTimestampBorderoFinal;
/*
    * @var String
    * @access Private
*/
var $stTimestampAnulacao;
/*
    * @var Array
    * @access Private
*/
var $arAssinatura;
/*
    * @var Array
    * @access Private
*/
var $arTransacaoPagamento;
/*
    * @var Array
    * @access Private
*/
var $arTransacaoTransferencia;
/*
    * @var Object
    * @access Private
*/
var $roUltimaTransacaoPagamento;
/*
    * @var Object
    * @access Private
*/
var $roUltimaTransacaoTransferencia;
/*
    * @var Object
    * @access Private
*/
var $roUltimaAssinatura;
var $stCodOrdem;
/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaAutenticacao($valor) { $this->obRTesourariaAutenticacao            = $valor; }
/*
    * @access Public
    * @param String $valor
*/

function setCodBordero($valor) { $this->inCodBordero                      = $valor; }
/*
    * @access Public
    * @param String $valor
*/

function setCodBorderoInicial($valor) { $this->inCodBorderoInicial               = $valor; }
/*
    * @access Public
    * @param String $valor
*/

function setCodBorderoFinal($valor) { $this->inCodBorderoFinal                 = $valor; }
/*
    * @access Public
    * @param String $valor
*/

function setExercicio($valor) { $this->stExercicio                        = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampBordero($valor) { $this->stTimestampBordero                  = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampBorderoInicial($valor) { $this->stTimestampBorderoInicial           = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampBorderoFinal($valor) { $this->stTimestampBorderoFinal             = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampAnulacao($valor) { $this->stTimestampAnulacao                 = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setTransacaoPagamento($valor) { $this->arTransacaoPagamento                = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setTransacaoTransferencia($valor) { $this->arTransacaoTransferencia            = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setAssinatura($valor) { $this->arAssinatura                        = $valor; }
function setCodOrdem($valor) { $this->stCodOrdem                          = $valor; }

/*
    * @access Public
    * @return Object
*/
function getRTesourariaAutenticacao() { return $this->obRTesourariaAutenticacao;                }
/*
    * @access Public
    * @return Integer
*/
function getCodBordero() { return $this->inCodBordero;                        }
/*
    * @access Public
    * @return Integer
*/
function getCodBorderoInicial() { return $this->inCodBorderoInicial;                 }
/*
    * @access Public
    * @return Integer
*/
function getCodBorderoFinal() { return $this->inCodBorderoFinal;                   }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                         }
/*
    * @access Public
    * @return String
*/
function getTimestampBordero() { return $this->stTimestampBordero;                  }
/*
    * @access Public
    * @return String
*/
function getTimestampBorderoInicial() { return $this->stTimestampBorderoInicial;           }
/*
    * @access Public
    * @return String
*/
function getTimestampBorderoFinal() { return $this->stTimestampBorderoFinal;             }
/*
    * @access Public
    * @return String
*/
function getTimestampAnulacao() { return $this->stTimestampAnulacao;                 }
/*
    * @access Public
    * @return Array
*/
function getTransacaoPagamento() { return $this->arTransacaoPagamento;                }
/*
    * @access Public
    * @return Array
*/
function getTransacaoTransferencia() { return $this->arTransacaoTransferencia;            }
/*
    * @access Public
    * @return Array
*/
function getAssinatura() { return $this->arAssinatura;                        }
function getCodOrdem() { return $this->stCodOrdem;                          }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaBordero(&$roRTesourariaBoletim)
{
    $this->obRTesourariaUsuarioTerminal   = new RTesourariaUsuarioTerminal( new RTesourariaTerminal );
    $this->obRTesourariaAssinatura        = new RTesourariaAssinatura();
    $this->obRContabilidadePlanoBanco     = new RContabilidadePlanoBanco();
    $this->obROrcamentoEntidade           = new ROrcamentoEntidade();
    $this->roRTesourariaBoletim           = $roRTesourariaBoletim;
    $this->obRTesourariaAutenticacao     = new RTesourariaAutenticacao();
}

/*
    * Método para adicionar Assinatura
    * @access Public
*/
function addAssinatura()
{
    $this->arAssinatura[] = new RTesourariaAssinatura();
    $this->roUltimaAssinatura = $this->arAssinatura[ count( $this->arAssinatura ) -1 ];
}
/*
    * Método para adicionar Transacao Pagamento
    * @access Public
*/
function addTransacaoPagamento()
{
    $this->arTransacaoPagamento[] = new RTesourariaTransacaoPagamento( $this );
    $this->roUltimaTransacaoPagamento = $this->arTransacaoPagamento[ count( $this->arTransacaoPagamento ) -1 ];
}
/*
    * Método para adicionar Transacao Transferencia
    * @access Public
*/
function addTransacaoTransferencia()
{
    $this->arTransacaoTransferencia[] = new RTesourariaTransacaoTransferencia( $this );
    $this->roUltimaTransacaoTransferencia = $this->arTransacaoTransferencia[ count( $this->arTransacaoTransferencia ) -1 ];
}

/*
    * Método para salvar Assinatura
    * @access Public
*/
function incluirAssinatura($boTransacao = "")
{
    $arAssinatura = $this->getAssinatura();

    foreach ($arAssinatura as $key => $obAssinatura) {

        if ($key == 0) {
            $obErro = $obAssinatura->excluir( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        } else {
            $obErro = $obAssinatura->incluir( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    return $obErro;
}
/*
    * Método para salvar Transacao Pagamento
    * @access Public
*/
function incluirTransacaoPagamento($boTransacao = "")
{
    $arTransacaoPagamento = $this->getTransacaoPagamento();

    foreach ($arTransacaoPagamento as $obTransacaoPagamento) {

        $obErro = $obTransacaoPagamento->incluir( $boTransacao );
        if ( $obErro->ocorreu() ) {
            break;
        }

    }

    return $obErro;
}
/*
    * Método para salvar Transacao Transferencia
    * @access Public
*/
function incluirTransacaoTransferencia($boTransacao = "")
{
    $arTransacaoTransferencia = $this->getTransacaoTransferencia();

    foreach ($arTransacaoTransferencia as $obTransacaoTransferencia) {

        $obErro = $obTransacaoTransferencia->incluir( $boTransacao );
        if ( $obErro->ocorreu() ) {
            break;
        }
    }

    return $obErro;
}

/**
    * Executa um proximoCodigo na Persistente
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaProximoCodigo($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"          );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaBordero.class.php" );
     $obTransacao               =  new Transacao;
     $obTTesourariaBordero      =  new TTesourariaBordero;

     $obTTesourariaBordero->setDado( "exercicio", $this->stExercicio );
     $obTTesourariaBordero->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
     $obErro = $obTTesourariaBordero->proximoCod( $inCodBordero, $obTransacao );
     $this->inCodBordero = $inCodBordero;

     return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBordero.class.php"  );
    $obTransacao                         = new Transacao();
    $obTTesourariaBordero                = new TTesourariaBordero();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRTesourariaBoletim->incluir( $boTransacao );
        if (!$obErro->ocorreu()) {
            $obErro = $this->buscaProximoCodigo($boTransacao);
            if (!$obErro->ocorreu()) {

                $obTTesourariaBordero->setDado( 'cod_bordero'             , $this->getCodBordero() );
                $obTTesourariaBordero->setDado( 'cod_entidade'            , $this->obROrcamentoEntidade->getCodigoEntidade() );
                $obTTesourariaBordero->setDado( 'cod_plano'               , $this->obRContabilidadePlanoBanco->getCodPlano() );
                $obTTesourariaBordero->setDado( 'cod_boletim'             , $this->roRTesourariaBoletim->getCodBoletim() );
                $obTTesourariaBordero->setDado( 'exercicio'               , $this->getExercicio() );
                $obTTesourariaBordero->setDado( 'exercicio_boletim'       , $this->roRTesourariaBoletim->getExercicio() );
                $obTTesourariaBordero->setDado( 'timestamp_bordero'       , $this->getTimestampBordero() );
                $obTTesourariaBordero->setDado( 'cod_autenticacao'        , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                $obTTesourariaBordero->setDado( 'dt_autenticacao'         , $this->roRTesourariaBoletim->getDataBoletim()        );
                $obTTesourariaBordero->setDado( 'cod_terminal'            , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal());
                $obTTesourariaBordero->setDado( 'cgm_usuario'             , $this->obROrcamentoEntidade->obRCGM->getNumCGM() );
                $obTTesourariaBordero->setDado( 'timestamp_terminal'      , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                $obTTesourariaBordero->setDado( 'timestamp_usuario'       , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario() );

                $obErro = $obTTesourariaBordero->inclusao( $boTransacao );

            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaBordero );

    return $obErro;

}

/**
    * Iinclui os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirTransacaoTransferenciaAssinatura($boTransacao = "")
{
    $this->obRTesourariaAutenticacao->setTipo("BR");
    $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
    $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());

    $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->incluir($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->incluirTransacaoTransferencia($boTransacao);
            if (!$obErro->ocorreu() ) {
                $obErro = $this->incluirAssinatura($boTransacao);
            }
        }
    }

    return $obErro;
}

/**
    * Inclui os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirTransacaoPagamentoAssinatura($boTransacao = "")
{
    $this->obRTesourariaAutenticacao->setTipo("BR");
    $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
    $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());

    $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->incluir($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->incluirTransacaoPagamento($boTransacao);
            if (!$obErro->ocorreu()) {
                $obErro = $this->incluirAssinatura($boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $nuVlTotal = 0;
                    $arTransacaoPagamento = $this->getTransacaoPagamento();
                    foreach ($arTransacaoPagamento as $obTransacaoPagamento) {

                        $inValor = $obTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getValorPagamento();
                        $inValor = str_replace(".","",$inValor);
                        $flValor = str_replace(",",".",$inValor);
                        $nuVlTotal = bcadd($nuVlTotal,$flValor,4);
                    }
                    $obErro = $this->montaDescricaoAutenticacao($nuVlTotal, $boTransacao);
                }
            }
        }
    }

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
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBordero.class.php"         );
    $obTTesourariaBordero = new TTesourariaBordero();

    if( $this->stExercicio )
        $stFiltro = " TB.exercicio = '".$this->stExercicio."' AND ";
        $obTTesourariaBordero->setDado('stExercicio', $this->stExercicio );

    if( $this->obRContabilidadePlanoBanco->inCodPlano )
        $stFiltro .= " TB.cod_plano = ".$this->obRContabilidadePlanoBanco->inCodPlano." AND ";

    if( $this->obROrcamentoEntidade->inCodigoEntidade )
       $stFiltro .= " TB.cod_entidade IN ( ".$this->obROrcamentoEntidade->inCodigoEntidade." ) AND ";

    if ($this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        if(( $this->inCodBorderoInicial == $this->inCodBorderoFinal ) &&
           ( $this->inCodBorderoInicial != '' && $this->inCodBorderoFinal != ''))
        {
            $stFiltro .= " TB.cod_bordero = ".$this->inCodBorderoInicial." AND ";
            $obTTesourariaBordero->setDado('cod_bordero', $this->inCodBorderoInicial );

        } else {
            $stFiltro .= " TB.cod_bordero BETWEEN ".$this->inCodBorderoInicial." AND ".$this->inCodBorderoFinal." AND ";
            $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);
            $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
        }

    } elseif ($this->inCodBorderoInicial and !$this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero >= ".$this->inCodBorderoInicial." AND ";
        $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);

    } elseif (!$this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero <= ".$this->inCodBorderoFinal." AND ";
        $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
    }

    if ($this->stTimestampBorderoInicial and $this->stTimestampBorderoFinal) {
        $stFiltro .= " TB.timestamp_bordero BETWEEN TO_DATE('".$this->stTimestampBorderoInicial."','dd//mm/yyyy') AND TO_DATE('".$this->stTimestampBorderoFinal."','dd/mm/yyyy') AND ";

    } elseif ($this->stTimestampBorderoInicial and !$this->stTimestampBorderoFinal) {
        $stFiltro .= " TB.timestamp_bordero >= TO_DATE('".$this->stTimestampBorderoInicial."','dd/mm/yyyy') AND ";

    } elseif (!$this->stTimestampBorderoInicial and $this->stTimestampBorderoFinal) {
        $stFiltro .= " TB.timestamp_bordero <= TO_DATE('".$this->stTimestampBorderoFinal."','dd/mm/yyyy') AND ";
    }

    if ($this->obROrcamentoEntidade->obRCGM->inNumCGM) {
       $stFiltro .= " ( TTP.cgm_beneficiario = ".$this->obROrcamentoEntidade->obRCGM->inNumCGM." OR  TTT.numcgm = ".$this->obROrcamentoEntidade->obRCGM->inNumCGM." ) AND ";
    }
    if ($this->stCodOrdem)
        $obTTesourariaBordero->setDado ('stCodORdem', $this->stCodOrdem );

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaBordero->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listaConsultaBorderos(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBordero.class.php"         );
    $obTTesourariaBordero = new TTesourariaBordero();

    if( $this->stExercicio )
        $stFiltro = " TB.exercicio = '".$this->stExercicio."' AND ";
        $obTTesourariaBordero->setDado('stExercicio', $this->stExercicio );

    if( $this->obRContabilidadePlanoBanco->inCodPlano )
        $stFiltro .= " TB.cod_plano = ".$this->obRContabilidadePlanoBanco->inCodPlano." AND ";

    if ($this->obROrcamentoEntidade->inCodigoEntidade) {
       $stFiltro .= " TB.cod_entidade IN ( ".$this->obROrcamentoEntidade->inCodigoEntidade." ) AND ";
       $obTTesourariaBordero->setDado('cod_entidade',  $this->obROrcamentoEntidade->inCodigoEntidade );
    }
    if ($this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        if(( $this->inCodBorderoInicial == $this->inCodBorderoFinal ) &&
           ( $this->inCodBorderoInicial != '' && $this->inCodBorderoFinal != ''))
        {
            $stFiltro .= " TB.cod_bordero = ".$this->inCodBorderoInicial." AND ";
            $obTTesourariaBordero->setDado('cod_bordero', $this->inCodBorderoInicial );

        } else {
            $stFiltro .= " TB.cod_bordero BETWEEN ".$this->inCodBorderoInicial." AND ".$this->inCodBorderoFinal." AND ";
            $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);
            $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
        }

    } elseif ($this->inCodBorderoInicial and !$this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero >= ".$this->inCodBorderoInicial." AND ";
        $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);

    } elseif (!$this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero <= ".$this->inCodBorderoFinal." AND ";
        $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
    }

    if ($this->stTimestampBorderoInicial and $this->stTimestampBorderoFinal) {
        $stFiltro .= " TB.timestamp_bordero BETWEEN TO_DATE('".$this->stTimestampBorderoInicial."','dd//mm/yyyy') AND TO_DATE('".$this->stTimestampBorderoFinal."','dd/mm/yyyy') AND ";

    } elseif ($this->stTimestampBorderoInicial and !$this->stTimestampBorderoFinal) {
        $stFiltro .= " TB.timestamp_bordero >= TO_DATE('".$this->stTimestampBorderoInicial."','dd/mm/yyyy') AND ";

    } elseif (!$this->stTimestampBorderoInicial and $this->stTimestampBorderoFinal) {
        $stFiltro .= " TB.timestamp_bordero <= TO_DATE('".$this->stTimestampBorderoFinal."','dd/mm/yyyy') AND ";
    }

    if ($this->obROrcamentoEntidade->obRCGM->inNumCGM) {
       $stFiltro .= " ( TTP.cgm_beneficiario = ".$this->obROrcamentoEntidade->obRCGM->inNumCGM." OR  TTT.numcgm = ".$this->obROrcamentoEntidade->obRCGM->inNumCGM." ) AND ";
    }
    if ($this->stCodOrdem) {
        $obTTesourariaBordero->setDado ('stCodOrdem', $this->stCodOrdem );
    } else {
        $obTTesourariaBordero->setDado ('stCodOrdem', $this->getListaOP() );
    }

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaBordero->recuperaListaBorderos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
//            $obTTesourariaBordero->debug();
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
function listarDadosBordero(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBordero.class.php"         );
    $obTTesourariaBordero = new TTesourariaBordero();

    if( $this->stExercicio )
        $stFiltro = " TB.exercicio = '".$this->stExercicio."' AND ";

    if( $this->obRContabilidadePlanoBanco->inCodPlano )
        $stFiltro .= " TB.cod_plano = ".$this->obRContabilidadePlanoBanco->inCodPlano." AND ";

    if( $this->obROrcamentoEntidade->inCodigoEntidade )
        $stFiltro .= " TB.cod_entidade IN ( ".$this->obROrcamentoEntidade->inCodigoEntidade." ) AND ";

    if ($this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero BETWEEN ".$this->inCodBorderoInicial." AND ".$this->inCodBorderoFinal." AND ";

    } elseif ($this->inCodBorderoInicial and !$this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero >= ".$this->inCodBorderoInicial." AND ";
        $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);

    } elseif (!$this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        $stFiltro .= " TB.cod_bordero <= ".$this->inCodBorderoFinal." AND ";
        $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
    }

    if ($this->stTimestampBorderoInicial and $this->stTimestampBorderoFinal) {
        $stFiltro .= " TO_DATE(TB.timestamp_bordero,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->stTimestampBorderoInicial."','dd//mm/yyyy') AND TO_DATE('".$this->stTimestampBorderoFinal."','dd/mm/yyyy') AND ";

    } elseif ($this->stTimestampBorderoInicial and !$this->stTimestampBorderoFinal) {
        $stFiltro .= " TO_DATE(TB.timestamp_bordero,'yyyy-mm-dd') >= TO_DATE('".$this->stTimestampBorderoInicial."','dd/mm/yyyy') AND ";

    } elseif (!$this->stTimestampBorderoInicial and $this->stTimestampBorderoFinal) {
        $stFiltro .= " TO_DATE(TB.timestamp_bordero,'yyyy-mm-dd') <= TO_DATE('".$this->stTimestampBorderoFinal."','dd/mm/yyyy') AND ";
    }

    if ($this->stCodOrdem) {
        $obTTesourariaBordero->setDado('stCodOrdem', $this->stCodOrdem );
    }
    $stFiltro .= " ttp.vl_pagamento > 0.00 AND ";

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $stOrder = $stOrder ? $stOrder : 'ORDER BY tb.cod_bordero ';
    $obErro = $obTTesourariaBordero->recuperaDadosBordero( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
function getListaOP($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBordero.class.php"         );
    $obTTesourariaBordero = new TTesourariaBordero();

    if( $this->stExercicio )
        $obTTesourariaBordero->setDado('exercicio', $this->stExercicio );

    if( $this->obROrcamentoEntidade->inCodigoEntidade )
        $obTTesourariaBordero->setDado('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade );

    if( $this->inCodBordero )
        $obTTesourariaBordero->setDado('cod_bordero', $this->inCodBordero );

    if ($this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        if(( $this->inCodBorderoInicial == $this->inCodBorderoFinal ) &&
           ( $this->inCodBorderoInicial != '' && $this->inCodBorderoFinal != ''))
        {
            $obTTesourariaBordero->setDado('cod_bordero', $this->inCodBorderoInicial );

        } else {
            $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);
            $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
        }

    } elseif ($this->inCodBorderoInicial and !$this->inCodBorderoFinal) {
        $obTTesourariaBordero->setDado('cod_bordero_inicial', $this->inCodBorderoInicial);

    } elseif (!$this->inCodBorderoInicial and $this->inCodBorderoFinal) {
        $obTTesourariaBordero->setDado('cod_bordero_final', $this->inCodBorderoFinal);
    }

    $obErro = $obTTesourariaBordero->recuperaListaOP( $rsOPBordero, $boTransacao );
    while (!$rsOPBordero->eof()) {
        $stOrdemPagamento .= $rsOPBordero->getCampo('cod_ordem').",";
        $rsOPBordero->proximo();
    }
    if ($stOrdemPagamento) {
        $stOrdemPagamento = substr($stOrdemPagamento,0,strlen($stOrdemPagamento)-1);
    }

    return $stOrdemPagamento;
}

function consultar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBordero.class.php"         );
    $obTTesourariaBordero = new TTesourariaBordero();

    if ($this->stExercicio) {
        $stFiltro = " TB.exercicio = '".$this->stExercicio."' AND ";
        $obTTesourariaBordero->setDado('stExercicio', $this->stExercicio );
    }
    if ($this->obROrcamentoEntidade->inCodigoEntidade) {
        $stFiltro .= " TB.cod_entidade IN ( ".$this->obROrcamentoEntidade->inCodigoEntidade." ) AND ";
        $obTTesourariaBordero->setDado('cod_entidade', $this->obROrcamentoEntidade->inCodigoEntidade );
    }
    if ($this->inCodBordero) {
        $stFiltro .= " TB.cod_bordero = ".$this->inCodBordero." AND ";
        $obTTesourariaBordero->setDado('cod_bordero ', $this->inCodBordero );
    }
    if( $this->stCodOrdem )
        $obTTesourariaBordero->setDado('stCodOrdem', $this->stCodOrdem );
    else {
        $obTTesourariaBordero->setDado ('stCodOrdem', $this->getListaOP() );
    }

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaBordero->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function montaDescricaoAutenticacao($nuVlTotal, $boTransacao = "")
{
    $obErro = new Erro;

    if ($this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getFormaComprovacao()==2) {
        $stDescricao = chr(15).$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getDigitos();
        $inCodAutenticacao = $this->obRTesourariaAutenticacao->getCodAutenticacao();
        $stDescricao .= str_pad($inCodAutenticacao, 8, "0", STR_PAD_LEFT) . " ";
        $stDescricao .= substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),0,6) . substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),8,2)." ";

        $stDescricao .= "BORDERO ";
        $stDescricao .= $this->obROrcamentoEntidade->getCodigoEntidade() ."-".$this->getCodBordero()."/".substr($this->getExercicio(),2,2)." ";
        $stDescricao = str_pad($stDescricao, (12 - strlen($this->obROrcamentoEntidade->getCodigoEntidade()) - strlen($this->getCodBordero())) , " ") . " ";
        $nuValor = number_format($nuVlTotal,"2",",",".");
        $stDescricao .= str_pad($nuValor, 14, "*", STR_PAD_LEFT) . "C\\n";

        $this->obRTesourariaAutenticacao->setDescricao(array($stDescricao));
    } else {
        $this->obRTesourariaAutenticacao->montaComprovante($cabecalho, $rodape, $boTransacao );

        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setCodModulo( 2 );
        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setParametro( "nom_prefeitura");
        $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setValor( null);
        $obErro = $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $nuValor = number_format($nuVlTotal,"2",",",".");

            $corpo = str_pad("COMPROVANTE DE RECEBIMENTO", 60, " ", STR_PAD_BOTH)."\\n\\n";
            $corpo .= wordwrap("Atesto que recebi(emos) da ".$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getValor()." o valor de R$ ".$nuValor."(".extenso($nuVlTotal)." ), relativos ao pagamento de boderô discriminado abaixo:", 60, "\\n")."\\n\\n";

            $corpo .= str_pad("OP", 20, " ", STR_PAD_BOTH);
            $corpo .= str_pad("Data", 20, " ", STR_PAD_BOTH);
            $corpo .= str_pad("Valor", 20, " ", STR_PAD_BOTH)."\\n";

            $arTransacaoPagamento = $this->getTransacaoPagamento();
            foreach ($arTransacaoPagamento as $obTransacaoPagamento) {

                $corpo .= str_pad($obTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$obTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getExercicio(), 20, " ", STR_PAD_BOTH);
                $corpo .= str_pad($obTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getDataEmissao(), 20, " ", STR_PAD_BOTH);
                $corpo .= str_pad(number_format($obTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getValorPagamento(),"2",",","."), 20, " ", STR_PAD_LEFT)."\\n";
            }

                $corpo .= "\\n".str_pad("Total", 40, " ",STR_PAD_LEFT);
                $corpo .= str_pad($nuValor, 20, " ", STR_PAD_LEFT)."\\n\\n";

            $stDescricao = chr(15).$cabecalho . $corpo . $rodape;

            $this->obRTesourariaAutenticacao->setDescricao(array(tiraAcentos($stDescricao)."\\n\\n\\n\\n\\n\\n\\n\\n\\n"));
        }

    }

    return $obErro;
}

}
