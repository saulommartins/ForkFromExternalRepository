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
     * Classe de regra de negócio para transferência de propriedade
     * Data de Criação: 02/12/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Vitor Davi Valentini
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMTransferencia.class.php 63839 2015-10-22 18:08:07Z franver $

     * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.13  2007/04/20 14:34:08  dibueno
Bug #9185#

Revision 1.12  2007/03/21 21:11:25  dibueno
Bug #8845#

Revision 1.11  2007/02/21 20:31:49  cercato
Bug #8474#

Revision 1.10  2006/10/27 17:52:38  dibueno
Verificações para exibição de transferencia. Efetivacao FALSE, caso seja só consulta

Revision 1.9  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaImovel.class.php"          );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaDocumento.class.php"       );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaProcesso.class.php"        );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaEfetivacao.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaCancelamento.class.php"    );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaCorretagem.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMMatriculaImovelTransferencia.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMProprietario.class.php"                 );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMExProprietario.class.php"               );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMAdquirente.class.php"                      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"                      );

class RCIMTransferencia
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTransferencia;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNatureza;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoMunicipal;
/**
    * @access Private
    * @var String
*/
var $stDataCadastro;
/**
    * @access Private
    * @var Integer
*/
var $inProcesso;
/**
    * @access Private
    * @var String
*/
var $stExercicioProcesso;
/**
    * @access Private
    * @var String
*/
var $stDataEfetivacao;
/**
    * @access Private
    * @var String
*/
var $stObservacao;
/**
    * @access Private
    * @var String
*/
var $stDataCancelamento;
/**
    * @access Private
    * @var String
*/
var $stMotivo;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroCGM;
/**
    * @access Private
    * @var Boolean
*/
var $boEfetivacao;
/**
    * @access Private
    * @var Array
*/
var $arAdquirentes;
/**
    * @access Private
    * @var Array
*/
var $arDocumentos;
/**
    * @access Private
    * @var Integer
*/
var $stMatriculaRegImov;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaImovel;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaDocumento;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaProcesso;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaEfetivacao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaCancelamento;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaCorretagem;
/**
    * @access Private
    * @var Object
*/
var $obTCIMProprietario;
/**
    * @access Private
    * @var Object
*/
var $obTCIMExProprietario;
/**
    * @access Private
    * @var Object
*/
var $obRCIMAdquirente;
/**
    * @access Private
    * @var Object
*/
var $obRCIMNaturezaTransferencia;
/**
    * @access Private
    * @var Object
*/
var $obRCIMCorretagem;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTransferencia($valor) { $this->inCodigoTransferencia  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNatureza($valor) { $this->inCodigoNatureza       = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoMunicipal($valor) { $this->inInscricaoMunicipal   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataCadastro($valor) { $this->stDataCadastro         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setProcesso($valor) { $this->inProcesso             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicioProcesso($valor) { $this->stExercicioProcesso    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataEfetivacao($valor) { $this->stDataEfetivacao       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setObservacao($valor) { $this->stObservacao           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataCancelamento($valor) { $this->stDataCancelamento     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMotivo($valor) { $this->stMotivo               = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumeroCGM($valor) { $this->inNumeroCGM            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEfetivacao($valor) { $this->boEfetivacao           = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setAdquirentes($valor) { $this->arAdquirentes          = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setDocumentos($valor) { $this->arDocumentos           = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setMatriculaRegImov($valor) { $this->stMatriculaRegImov     = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoTransferencia() { return $this->inCodigoTransferencia;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNatureza() { return $this->inCodigoNatureza;       }
/**
    * @access Public
    * @return Integer
*/
function getInscricaoMunicipal() { return $this->inInscricaoMunicipal;   }
/**
    * @access Public
    * @return String
*/
function getDataCadastro() { return $this->stDataCadastro;         }
/**
    * @access Public
    * @return Integer
*/
function getProcesso() { return $this->inProcesso;             }
/**
    * @access Public
    * @return String
*/
function getExercicioProcesso() { return $this->stExercicioProcesso;    }
/**
    * @access Public
    * @return String
*/
function getDataEfetivacao() { return $this->stDataEfetivacao;       }
/**
    * @access Public
    * @return String
*/
function getObservacao() { return $this->stObservacao;           }
/**
    * @access Public
    * @return String
*/
function getDataCancelamento() { return $this->stDataCancelamento;     }
/**
    * @access Public
    * @return String
*/
function getMotivo() { return $this->stMotivo;               }
/**
    * @access Public
    * @return Integer
*/
function getNumeroCGM() { return $this->inNumeroCGM;            }
/**
    * @access Public
    * @return Boolean
*/
function getEfetivacao() { return $this->boEfetivacao;           }
/**
    * @access Public
    * @return Array
*/
function getAdquirentes() { return $this->arAdquirentes;          }
/**
    * @access Public
    * @return Array
*/
function getDocumentos() { return $this->arDocumentos;           }
/**
    * @access Public
    * @return Array
*/
function getMatriculaRegImov() { return $this->stMatriculaRegImov;     }

/**
     * Método construtor
     * @access Private
*/
function RCIMTransferencia()
{
    $this->obTCIMTransferenciaImovel          = new TCIMTransferenciaImovel;
    $this->obTCIMTransferenciaDocumento       = new TCIMTransferenciaDocumento;
    $this->obTCIMTransferenciaProcesso        = new TCIMTransferenciaProcesso;
    $this->obTCIMTransferenciaEfetivacao      = new TCIMTransferenciaEfetivacao;
    $this->obTCIMTransferenciaCancelamento    = new TCIMTransferenciaCancelamento;
    $this->obTCIMTransferenciaCorretagem      = new TCIMTransferenciaCorretagem;
    $this->obTCIMMatriculaImovelTransferencia = new TCIMMatriculaImovelTransferencia;
    $this->obTCIMProprietario                 = new TCIMProprietario;
    $this->obTCIMExProprietario               = new TCIMExProprietario;
    $this->obRCIMAdquirente                   = new RCIMAdquirente;
    $this->obRCIMNaturezaTransferencia        = new RCIMNaturezaTransferencia;
    $this->obRCIMCorretagem                   = new RCIMCorretagem;
    $this->obTransacao                        = new Transacao;
}
/**
    * Inclui os dados setados na tabela de Transferência de Propriedade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function cadastrarTransferencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCIMTransferenciaImovel->proximoCod( $this->inCodigoTransferencia, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMTransferenciaImovel->setDado( "cod_transferencia"  , $this->inCodigoTransferencia );
            $this->obTCIMTransferenciaImovel->setDado( "cod_natureza"       , $this->inCodigoNatureza      );
            $this->obTCIMTransferenciaImovel->setDado( "inscricao_municipal", $this->inInscricaoMunicipal  );
            $obErro = $this->obTCIMTransferenciaImovel->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->obRCIMCorretagem->getRegistroCreci() != '' ) {
                    $this->obTCIMTransferenciaCorretagem->setDado( "cod_transferencia" , $this->inCodigoTransferencia    );
                    $this->obTCIMTransferenciaCorretagem->setDado( "creci" , $this->obRCIMCorretagem->getRegistroCreci() );
                    $obErro = $this->obTCIMTransferenciaCorretagem->inclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->salvarDocumentos( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obRCIMAdquirente->setCodigoTransferencia( $this->inCodigoTransferencia );
                        $this->obRCIMAdquirente->setAdquirentes( $this->arAdquirentes );
                        $obErro = $this->obRCIMAdquirente->incluirAdquirente( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->salvarProcesso( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaImovel );

    return $obErro;
}
/**
    * Altera os dados da Natureza de Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarTransferencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMTransferenciaImovel->setDado( "cod_transferencia"  , $this->inCodigoTransferencia );
        $this->obTCIMTransferenciaImovel->setDado( "inscricao_municipal", $this->inInscricaoMunicipal  );
        $this->obTCIMTransferenciaImovel->setDado( "cod_natureza"       , $this->inCodigoNatureza      );
        $obErro = $this->obTCIMTransferenciaImovel->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRCIMCorretagem->getRegistroCreci() != '' ) {
                $this->obTCIMTransferenciaCorretagem->setDado( "cod_transferencia" , $this->inCodigoTransferencia    );
                $this->obTCIMTransferenciaCorretagem->setDado( "creci" , $this->obRCIMCorretagem->getRegistroCreci() );
                $obErro = $this->obTCIMTransferenciaCorretagem->alteracao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->salvarDocumentos( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obRCIMAdquirente->setCodigoTransferencia( $this->inCodigoTransferencia );
                    $this->obRCIMAdquirente->setAdquirentes( $this->arAdquirentes );
                    $obErro = $this->obRCIMAdquirente->alterarAdquirente( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->salvarProcesso( $boTransacao );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaImovel );

    return $obErro;
}
/**
    * Efetua a Transferência de Propriedade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetivarTransferencia($boTransacao = "", $boAbstracao = true)
{
    $obErro = new Erro;
    if ( $boAbstracao )
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->consultarAdquirentes( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->consultarTransferencia( $boTransacao );
            if (!$obErro->ocorreu() ) {
                $obErro = $this->salvarDocumentos( $boTransacao );

                if ( !$obErro->ocorreu() ) {

                    $this->obTCIMTransferenciaEfetivacao->setDado( "cod_transferencia", $this->inCodigoTransferencia );
                    $this->obTCIMTransferenciaEfetivacao->setDado( "dt_efetivacao"    , $this->stDataEfetivacao      );
                    $this->obTCIMTransferenciaEfetivacao->setDado( "observacao"       , $this->stObservacao          );
                    $obErro = $this->obTCIMTransferenciaEfetivacao->inclusao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {

                        if ($this->stMatriculaRegImov) {
                            $this->obTCIMMatriculaImovelTransferencia->setDado( "cod_transferencia"  , $this->inCodigoTransferencia);
                            $this->obTCIMMatriculaImovelTransferencia->setDado( "mat_registro_imovel", $this->stMatriculaRegImov);
                            $obErro = $this->obTCIMMatriculaImovelTransferencia->inclusao( $boTransacao );
                        }
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->inserirExProprietario( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                    $obErro = $this->salvarProprietario( $boTransacao );
                            }
                        }

                    }

                    if ( !$obErro->ocorreu() && $this->getCodigoNatureza() == 1 ) {
                        //repassa as dividas em aberto para os novos CGMs quando a natureza for COMPRA/VENDA
                        #$arAdquirentes = $this->obRCIMAdquirente->getAdquirentes();
                        include_once( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );
                        include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaImovel.class.php" );
                        include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCGM.class.php" );

                        $obTDATDividaImovel = new TDATDividaImovel;
                        $obTDATDividaCGM = new TDATDividaCGM;
                        $stFiltro = " AND divida_imovel.inscricao_municipal = ".$this->getInscricaoMunicipal();
                        $obTDATDividaImovel->listaDividasEmAberto( $rsDividas, $stFiltro, $boTransacao );
                        while ( !$rsDividas->Eof() ) {
                            //deletar cgm antigo na inscricao
                            $obTDATDividaCGM->setDado( 'cod_inscricao', $rsDividas->getCampo( "cod_inscricao" ) );
                            $obTDATDividaCGM->setDado( 'exercicio', $rsDividas->getCampo( "exercicio" ) );
                            $obTDATDividaCGM->setDado( 'numcgm', "" );
                            $obTDATDividaCGM->exclusao( $boTransacao );

                            //adicionar cgm novo na inscricao
                            $contAdquirentes = 0;
                            while ( $contAdquirentes < count( $this->arAdquirentes ) ) {
                                $inCodCGMAdquirenteAtual = $this->arAdquirentes[$contAdquirentes]['codigo'];
                                $obTDATDividaCGM->setDado( 'numcgm', $inCodCGMAdquirenteAtual );
                                $obTDATDividaCGM->setDado( 'cod_inscricao', $rsDividas->getCampo( "cod_inscricao" ) );
                                $obTDATDividaCGM->setDado( 'exercicio', $rsDividas->getCampo( "exercicio" ) );
                                $obTDATDividaCGM->inclusao( $boTransacao );
                                $contAdquirentes++;
                            }

                            $rsDividas->proximo();
                        }

                        $this->obRARRCalculo = new RARRCalculo;

                        $this->obRARRCalculo->obRCIMImovel->setNumeroInscricao( $this->getInscricaoMunicipal() );
                        $obErro = $this->obRARRCalculo->listarCalculosAbertoImovel ( $rsCalculos, $boTransacao );
                        $rsCalculos->setPrimeiroElemento();

                        if ( !$obErro->ocorreu() ) {
                            include_once( CAM_GT_ARR_MAPEAMENTO."TARRCalculoCgm.class.php" );
                            $obTARRCalculoCgm = new TARRCalculoCgm;

                            $arBoCalculosExcluir = array();
                            while ( !$rsCalculos->eof() && !$obErro->ocorreu() ) {

                                $contAdquirentes = 0;
                                while ( $contAdquirentes < count( $this->arAdquirentes ) ) {
                                    $inCodCGMAdquirenteAtual = $this->arAdquirentes[$contAdquirentes]['codigo'];
                                    $obTARRCalculoCgm->setDado( 'cod_calculo', $rsCalculos->getCampo('cod_calculo') );
                                    $obTARRCalculoCgm->setDado( 'numcgm', $inCodCGMAdquirenteAtual );

                                    # VERIFICA SE O CALCULO JAH EXISTE PARA O CGM
                                    $stFiltro =  " WHERE numcgm = ".$inCodCGMAdquirenteAtual;
                                    $stFiltro .= " AND cod_calculo = ".$rsCalculos->getCampo('cod_calculo');
                                    $obTARRCalculoCgm->recuperaTodos ( $rsCalculoExiste, $stFiltro, $stOrdem, $boTransacao);

                                    if ( $rsCalculoExiste->getNumLinhas() < 1 ) {
                                        $obErro = $obTARRCalculoCgm->inclusao( $boTransacao );
                                    } else {
                                        $arBoCalculosExcluir[$rsCalculos->getCampo('cod_calculo').'-'.$inCodCGMAdquirenteAtual] = FALSE;
                                    }
                                    $contAdquirentes++;
                                }
                                $rsCalculos->proximo();
                            }

                            $rsCalculos->setPrimeiroElemento();
                            while ( !$rsCalculos->eof() && !$obErro->ocorreu() ) {
                                if ( !isset($arBoCalculosExcluir[$rsCalculos->getCampo('cod_calculo').'-'.$rsCalculos->getCampo('numcgm')]) ) {
                                    $obTARRCalculoCgm->setDado( 'cod_calculo', $rsCalculos->getCampo('cod_calculo') );
                                    $obTARRCalculoCgm->setDado( 'numcgm', $rsCalculos->getCampo('numcgm') );
                                    $obTARRCalculoCgm->exclusao( $boTransacao );
                                }
                                $rsCalculos->proximo();
                            }

                        }

                    }
                }
            }
        }

    }

    if ( $boAbstracao )
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaEfetivacao );

    return $obErro;
}
/**
    * Faz validação se todos os documentos necessarios foram entregues
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validarDocumentosEfetivacao(&$boOk, $boTransacao)
{
    /* buscar documentos necessarios para efetivação */
    require_once(CAM_GT_CIM_MAPEAMENTO.'TCIMDocumentoNatureza.class.php');
    $obTCIMDocumentoNatureza = new TCIMDocumentoNatureza;
    $stFiltro = " WHERE documento_natureza.transferencia = true and documento_natureza.cod_natureza = ".$this->getCodigoNatureza();
    $obErro = $obTCIMDocumentoNatureza->recuperaTodos( $rsDocumentosNatureza, $stFiltro, $stOrdem, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        /* buscar documentos entregues */
        $stFiltro = " WHERE cod_transferencia = ".$this->getCodigoTransferencia();
        $obErro = $this->obTCIMTransferenciaDocumento->recuperaTodos( $rsDocumentos, $stFiltro, $stOrdem, $boTransacao );
        if ( !$obErro->ocorreu() && $rsDocumentos->getNumLinhas() > 0 ) {
            /* busca documentos na lista de documentos entregues */
            $inNumNecessarios = $rsDocumentosNatureza->getNumLinhas();
            $inEnt = 0;
            while ( !$rsDocumentosNatureza->eof()  ) :
                $rsDocumentos->setPrimeiroElemento();
                while ( !$rsDocumentos->eof()  ) :
                    if ( $rsDocumentosNatureza->getCampo('cod_documento') == $rsDocumentos->getCampo('cod_documento') )
                        $inEnt++;
                    $rsDocumentos->proximo();
                endwhile;
                $rsDocumentosNatureza->proximo();
            endwhile;
            if ($inEnt == $inNumNecessarios) {
                $boOk = true;
            }
        } else {
            $boOk = true;
        }
    }

    return $obErro;
}

/**
    * Efetua a Transferência de Propriedade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function cancelarTransferencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMTransferenciaCancelamento->setDado( "cod_transferencia", $this->inCodigoTransferencia );
        $this->obTCIMTransferenciaCancelamento->setDado( "dt_cancelamento"  , $this->stDataCancelamento    );
        $this->obTCIMTransferenciaCancelamento->setDado( "motivo"           , $this->stMotivo              );
        $obErro = $this->obTCIMTransferenciaCancelamento->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaCancelamento );

    return $obErro;
}
/**
    * Recupera do banco de dados os dados da Transferência selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarTransferencia($boTransacao = "")
{
    if ( !$this->getEfetivacao() ) {
        $this->setEfetivacao  ('t');
    }
    $obErro = $this->listarTransferencia( $rsTransferencia, $boTransacao ) ;
    if ( $rsTransferencia->getNumLinhas() < 1 ) {
        $obErro->setDescricao( "Nenhuma transferência foi encontrada!");
    }

    if ( !$obErro->ocorreu() ) {
        $this->inInscricaoMunicipal = $rsTransferencia->getCampo( "inscricao_municipal" );
        $this->inCodigoNatureza     = $rsTransferencia->getCampo( "cod_natureza"        );
        $this->obTCIMTransferenciaProcesso->setDado( "cod_transferencia", $this->inCodigoTransferencia );
        $obErro = $this->obTCIMTransferenciaProcesso->recuperaPorChave( $rsTransferencia, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->inProcesso          = $rsTransferencia->getCampo( "cod_processo" );
            $this->stExercicioProcesso = $rsTransferencia->getCampo( "exercicio" );

            $this->obTCIMTransferenciaCorretagem->setDado('cod_transferencia', $this->inCodigoTransferencia);
            $this->obTCIMTransferenciaCorretagem->recuperaPorChave( $rsTemp, $boTransacao );
            $this->obRCIMCorretagem->setRegistroCreci($rsTemp->getCampo('creci'));
            $this->obRCIMCorretagem->buscaCorretagem( $rsTemp, $boTransacao );
            $this->obRCIMCorretagem->setNomCgmCreci( $rsTemp->getCampo('nom_cgm') );

            $this->obTCIMMatriculaImovelTransferencia->setDado( 'cod_transferencia', $this->inCodigoTransferencia );
            $this->obTCIMMatriculaImovelTransferencia->recuperaPorChave( $rsTemp, $boTransacao );
            $this->setMatriculaRegImov($rsTemp->getCampo('mat_registro_imovel'));
        }
    }

    return $obErro;
}
/**
    * Recupera do banco de dados os dados de Documentos da Transferência selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDocumentos($boTransacao = "")
{
    $this->obRCIMNaturezaTransferencia->setCodigoNatureza ( $this->inCodigoNatureza );
    $this->obRCIMNaturezaTransferencia->consultarDocumentosNatureza($boTransacao);

    $this->arDocumentos = $this->obRCIMNaturezaTransferencia->getDocumentosNatureza();

    $inTotalArray = count( $this->arDocumentos ) - 1;
    for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
         if ($this->inCodigoTransferencia != '') {
             $this->obTCIMTransferenciaDocumento->setDado( "cod_transferencia", $this->inCodigoTransferencia            );
             $this->obTCIMTransferenciaDocumento->setDado( "cod_documento"    , $this->arDocumentos[$inCount]['codigo'] );
             $obErro = $this->obTCIMTransferenciaDocumento->recuperaPorChave( $rsDocumentos, $boTransacao );
             if ( !$obErro->ocorreu() ) {
                 if ( $rsDocumentos->getNumLinhas() > 0 ) {
                     $this->arDocumentos[$inCount]['entregue'] = 't';
                 }
             }
         }
    }

    return $obErro;
}
/**
    * Recupera do banco de dados os dados os Adquirentes da Transferência selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarAdquirentes($boTransacao = "")
{
    $this->obRCIMAdquirente->setCodigoTransferencia( $this->inCodigoTransferencia );
    $obErro = $this->obRCIMAdquirente->consultarAdquirentes( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->arAdquirentes = $this->obRCIMAdquirente->getAdquirentes();
    }

    return $obErro;
}
/**
    * Lista os dados da Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTransferencia(&$rsTransferencia, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoTransferencia) {
        $stFiltro .= " TI.COD_TRANSFERENCIA = ".$this->inCodigoTransferencia." AND";
    }
    if ($this->inInscricaoMunicipal) {
        $stFiltro .= " TI.INSCRICAO_MUNICIPAL = ".$this->inInscricaoMunicipal." AND";
    }
    if ($this->inCodigoNatureza) {
        $stFiltro .= " TI.COD_NATUREZA = ".$this->inCodigoNatureza." AND";
    }
    if ($this->inNumeroCGM) {
        $stFiltro .= " TA.NUMCGM = ".$this->inNumeroCGM." AND";
    }
    if ($this->boEfetivacao == 't') {
        $stFiltro .= " TE.DT_EFETIVACAO IS NULL AND";
        $stFiltro .= " TC.DT_CANCELAMENTO IS NULL AND";
    }
    if ($this->boEfetivacao == 'f') {
        $stFiltro .= " TE.DT_EFETIVACAO IS NOT NULL AND";
        $stFiltro .= " TC.DT_CANCELAMENTO IS NULL AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY TI.COD_TRANSFERENCIA,TI.INSCRICAO_MUNICIPAL ";
    $obErro = $this->obTCIMTransferenciaImovel->recuperaTransferenciaImovel( $rsTransferencia, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
/**
    * Gravar os Documentos da Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarDocumentos($boTransacao = "")
{
    $obConexao   = new Conexao;
    $stSql = " delete from ".$this->obTCIMTransferenciaDocumento->getTabela()." where cod_transferencia = ".$this->inCodigoTransferencia;
    $obErro = $obConexao->executaDML($stSql,$boTransacao);
    if ( !$obErro->ocorreu() ) {
        $boGravarDocumentos = 't';
        $inTotalArray = count( $this->arDocumentos ) - 1;
        for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
             if ($this->boEfetivacao == 'f') {
                 if ($this->arDocumentos[$inCount]['obrigatorio'] == "Cadastro" AND $this->arDocumentos[$inCount]['entregue'] == 'f') {
                     $boGravarDocumentos = 'f';
                     $obErro->setDescricao( "Documentos com obrigatoriedade de Cadastro não foram entregues!" );
                     break;
                 }
             } else {
                 if ($this->arDocumentos[$inCount]['obrigatorio'] == "Efetivação" AND $this->arDocumentos[$inCount]['entregue'] == 'f') {
                     $boGravarDocumentos = 'f';
                     $obErro->setDescricao( "Documentos com obrigatoriedade de Efetivação não foram entregues!" );                 break;
                 }
             }
        }
        if ($boGravarDocumentos == 't') {
            for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
                 if ($this->arDocumentos[$inCount]['entregue'] == 't') {
                     $this->obTCIMTransferenciaDocumento->setDado( "cod_transferencia", $this->inCodigoTransferencia            );
                     $this->obTCIMTransferenciaDocumento->setDado( "cod_documento"    , $this->arDocumentos[$inCount]['codigo'] );
                     $obErro = $this->obTCIMTransferenciaDocumento->inclusao( $boTransacao );
                 }
             }
        }
    }

    return $obErro;
}
function salvarProcesso($boTransacao = "")
{
    $this->obTCIMTransferenciaProcesso->setDado( "cod_transferencia", $this->inCodigoTransferencia );
    $obErro = $this->obTCIMTransferenciaProcesso->exclusao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($this->inProcesso != '') {
            $this->obTCIMTransferenciaProcesso->setDado( "cod_transferencia", $this->inCodigoTransferencia );
            $this->obTCIMTransferenciaProcesso->setDado( "cod_processo"     , $this->inProcesso            );
            $this->obTCIMTransferenciaProcesso->setDado( "exercicio"        , $this->stExercicioProcesso   );
            $obErro = $this->obTCIMTransferenciaProcesso->inclusao( $boTransacao );
        }
    }

    return $obErro;
}
function inserirExProprietario($boTransacao = "")
{
    $stFiltro = " WHERE inscricao_municipal= ".$this->getInscricaoMunicipal();
    $obErro = $this->obTCIMProprietario->recuperaTodos( $rsProprietario, $stFiltro, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsProprietario->eof() ) {
            $this->obTCIMExProprietario->setDado("inscricao_municipal",$rsProprietario->getCampo("inscricao_municipal"));
            $this->obTCIMExProprietario->setDado("numcgm"             ,$rsProprietario->getCampo("numcgm"             ));
            $this->obTCIMExProprietario->setDado("ordem"              ,$rsProprietario->getCampo("ordem"              ));
            $this->obTCIMExProprietario->setDado("cota"               ,$rsProprietario->getCampo("cota"               ));
            $obErro = $this->obTCIMExProprietario->inclusao( $boTransacao );
            $rsProprietario->proximo();
        }
    }

    return $obErro;
}
function salvarProprietario($boTransacao = "")
{
    $obTCIMProprietario = new TCIMProprietario;
    $obTCIMProprietario->setDado( "inscricao_municipal", $this->inInscricaoMunicipal );
    $obErro = $obTCIMProprietario->exclusao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inTotalArray = count( $this->arAdquirentes ) - 1;
        for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
             $obTCIMProprietario->setDado( "inscricao_municipal", $this->inInscricaoMunicipal              );
             $obTCIMProprietario->setDado( "numcgm"             , $this->arAdquirentes[$inCount]['codigo'] );
             $obTCIMProprietario->setDado( "ordem"              , $inCount + 1                             );
             $obTCIMProprietario->setDado( "cota"               , $this->arAdquirentes[$inCount]['quota']  );
             $obTCIMProprietario->setDado( "promitente"         , 'f'                                      );
             $obErro = $obTCIMProprietario->inclusao( $boTransacao );
        }
    }

    return $obErro;
}

function verificaPagamentoImovelITBI(&$rsPagamento, $boTransacao = "")
{
    if ($this->inInscricaoMunicipal) {
        $stFiltro .= " IC.INSCRICAO_MUNICIPAL = ".$this->inInscricaoMunicipal." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem =" order by cod_calculo DESC limit 1";
    $obErro = $this->obTCIMTransferenciaImovel->recuperaPagamentoImovelITBI( $rsPagamento,$stFiltro, $stOrdem, $boTransacao );
    return $obErro;
}
}
?>
