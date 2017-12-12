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
     * Classe de regra de negócio para condomínio
     * Data de Criação: 24/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Marcelo Boezzio Paulino

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMCondominio.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.14
*/

/*
$Log$
Revision 1.10  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                         );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                     );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCondominioProcesso.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCondominio.class.php"          );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCondominioCgm.class.php"       );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCondominioAreaComum.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCondominioProcesso.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoCondominio.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteCondominio.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelCondominio.class.php"      );

//CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoCondominioValor.class.php" );

class RCIMCondominio
{
/**
* @access Private
* @var Integer
*/
var $inCodigoCondominio;
/**
* @access Private
* @var Integer
*/
var $inCodigoTipo;
/**
* @access Private
* @var Integer
*/
var $stNomTipo;
/**
* @access Private
* @var String
*/
var $stNomCondominio;
/**
* @access Private
* @var Numeric
*/
var $nuAreaTotalComum;
/**
* @access Private
* @var Object
*/
var $obTCIMCondominio;
/**
* @access Private
* @var Object
*/
var $obTCIMCondominioCgm;
/**
* @access Private
* @var Object
*/
var $obTCIMCondominioAreaComum;
/**
* @access Private
* @var Object
*/
var $obTCIMCondominioProcesso;
/**
* @access Private
* @var Object
*/
var $obTCIMTipoCondominio;
/**
* @access Private
* @var Object
*/
var $obTCIMLoteCondominio;
/**
* @access Private
* @var Object
*/
var $obRCGM;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/**
    * @access Private
    * @var Object
*/
var $obRProcesso;
/**
    * @access Private
    * @var Object
*/
var $obRCIMLote;
/**
    * @access Private
    * @var Timestamp
*/
var $tmTimestampCondominio;
/**
    * @acess Public
    * @var Array
*/
var $arLote = array();

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setCodigoCondominio($valor) { $this->inCodigoCondominio = $valor; }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoTipo($valor) { $this->inCodigoTipo   = $valor;      }
/**
* @access Public
* @param Integer $valor
*/
function setNomeTipo($valor) { $this->stNomTipo      = $valor;      }
/**
* @access Public
* @param String $valor
*/
function setNomCondominio($valor) { $this->stNomCondominio = $valor;     }
/**
* @access Public
* @param String $valor
*/
function setAreaTotalComum($valor) { $this->nuAreaTotalComum = $valor;    }
/**
* @access Public
* @param String $valor
*/
function setTimestampCondominio($valor) { $this->tmTimestampCondominio = $valor;    }

//GETTERS
/**
* @access Public
* @return Integer
*/
function getCodigoCondominio() { return $this->inCodigoCondominio; }
/**
* @access Public
* @return Integer
*/
function getCodigoTipo() { return $this->inCodigoTipo;       }
/**
* @access Public
* @return Integer
*/
function getNomeTipo() { return $this->stNomTipo;          }
/**
* @access Public
* @return String
*/
function getNomCondominio() { return $this->stNomCondominio;    }
/**
* @access Public
* @return String
*/
function getAreaTotalComum() { return $this->nuAreaTotalComum;   }
/**
* @access Public
* @return Timestamp
*/
function getTimestampCondominio() { return $this->tmTimestampCondominio;   }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCIMCondominio()
{
    $this->obTCIMCondominio           = new TCIMCondominio;
    $this->obTCIMCondominioCgm        = new TCIMCondominioCgm;
    $this->obTCIMCondominioAreaComum  = new TCIMCondominioAreaComum;
    $this->obTCIMCondominioProcesso   = new TCIMCondominioProcesso;
    $this->obTCIMTipoCondominio       = new TCIMTipoCondominio;
    $this->obTCIMLoteCondominio       = new TCIMLoteCondominio;
    $this->obTCIMImovelCondominio     = new TCIMImovelCondominio;
    $this->obTCIMCondominiolProcesso  = new TCIMCondominioProcesso;
    $this->obTransacao                = new Transacao;
    $this->obRCGM                     = new RCGM;
    $this->obRProcesso                = new RProcesso;
    $this->obRCadastroDinamico        = new RCadastroDinamico;

    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoCondominioValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 6 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
}

/**
* Inclui os dados setados na tabela de Condominio
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirCondominio($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCIMCondominio->proximoCod( $this->inCodigoCondominio , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->validaNomeCondominio( $rsCondominios, $boTransacao );
            if ( $rsCondominios->getNumLinhas() < 1 ) {
                $this->obTCIMCondominio->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                $this->obTCIMCondominio->setDado( "cod_tipo"       , $this->getCodigoTipo()     );
                $this->obTCIMCondominio->setDado( "nom_condominio" , $this->getNomCondominio()  );
                $obErro = $this->obTCIMCondominio->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( $this->obRCGM->getNumCGM() ) {
                        $this->obTCIMCondominioCgm->setDado( "numcgm"         , $this->obRCGM->getNumCGM() );
                        $this->obTCIMCondominioCgm->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                        $obErro = $this->obTCIMCondominioCgm->inclusao( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $arChaveAtributoCondominio = array( "cod_condominio" => $this->inCodigoCondominio );
                        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCondominio );
                        $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() AND $this->obRProcesso->getCodigoProcesso() ) {
                        $obErro = $this->salvarProcesso( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->salvarArea( $boTransacao );
                    }
                }
                if ( !$obErro->ocorreu() ) {

                    $this->obTCIMLoteCondominio->setDado ( 'cod_condominio', $this->inCodigoCondominio );
                    $this->obTCIMImovelCondominio->setDado('cod_condominio', $this->inCodigoCondominio );

                    $cont = 0;
                    $arLotes = $this->arLote;
                    $nLotes = count ( $arLotes );

                    while ($cont < $nLotes) {

                        $this->obTCIMLoteCondominio->setDado ( 'cod_lote', $arLotes[$cont]['inCodigoLote'] );

                        $obErro = $this->obTCIMLoteCondominio->inclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {

                            $obRCIMLote = new RCIMLote;
                            $obRCIMLote->setCodigoLote ( $arLotes[$cont]['inCodigoLote']);
                            $obRCIMLote->listarImoveisLote ( $rsImoveis, $boTransacao );

                            while ( !$rsImoveis->eof() ) {

                                $this->obTCIMImovelCondominio->setDado ( 'inscricao_municipal', $rsImoveis->getCampo ('inscricao_municipal'));
                                $obErro = $this->obTCIMImovelCondominio->inclusao ($boTransacao);
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                $rsImoveis->proximo();
                            }
                        }
                        $cont++;
                    }
                }
            } else {
                $obErro->setDescricao("Condomínio ".$this->getNomCondominio()." já cadastrado.");
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCondominio );

    return $obErro;
}

/**
* Altera os dados do Condominio
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarCondominio($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->validaNomeCondominio( $rsCondominios, $boTransacao );
        if ( $rsCondominios->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Nome de condomínio já utilizado');
        } else {

        $this->obTCIMCondominio->setDado( "cod_condominio" , $this->inCodigoCondominio  );
        $this->listarCondominios ($rsLista);

        $this->obTCIMCondominio->setDado( "cod_tipo"       , $this->getCodigoTipo()     );
        $this->obTCIMCondominio->setDado( "nom_condominio" , $this->getNomCondominio()  );
        $obErro = $this->obTCIMCondominio->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {

            if ( $this->obRCGM->getNumCGM() ) {
                $this->obTCIMCondominioCgm->setDado('cod_condominio', $this->inCodigoCondominio);
                $this->obTCIMCondominioCgm->consultar();

                if ($this->obTCIMCondominioCgm->getDado('numcgm')) {
                    $this->obTCIMCondominioCgm->setDado( "numcgm"         , $this->obRCGM->getNumCGM() );
                    $this->obTCIMCondominioCgm->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                    $obErro = $this->obTCIMCondominioCgm->alteracao( $boTransacao );
                } else {
                    $this->obTCIMCondominioCgm->setDado( "numcgm"         , $this->obRCGM->getNumCGM() );
                    $this->obTCIMCondominioCgm->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                    $obErro = $this->obTCIMCondominioCgm->inclusao( $boTransacao );
                }
            }
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoCondominio = array( "cod_condominio" => $this->inCodigoCondominio );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCondominio );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->salvarArea( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    if ( $this->obRProcesso->getCodigoProcesso() ) {
                        $obErro = $this->salvarProcesso( $boTransacao );
                    }
                }

                if ( !$obErro->ocorreu() ) {

                    $this->obTCIMLoteCondominio->setDado  ( 'cod_condominio', $this->inCodigoCondominio );
                    $this->obTCIMImovelCondominio->setDado( 'cod_condominio', $this->inCodigoCondominio );

                    $arLotes = $this->arLote;

                    $nLotes = count ( $arLotes );

                    //------------------------------------------------------------------------ exlcui lotes e imoveis
                    $obErro = $this->obTCIMLoteCondominio->exclusao ( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->obTCIMImovelCondominio->exclusao( $boTransacao );
                    }
                    //------------------------------------------------------------------------ exlcui lotes e imoveis
                    $cont = 0;
                    $obRCIMLote = new RCIMLote;
                    while ($cont < $nLotes) {
                        $this->obTCIMLoteCondominio->setDado ( 'cod_lote', $arLotes[$cont]['inCodigoLote'] );
                        $obErro = $this->obTCIMLoteCondominio->inclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obRCIMLote->setCodigoLote ( $arLotes[$cont]['inCodigoLote']);
                            $obRCIMLote->listarImoveisLote ( $rsImoveis, $boTransacao );
                            while ( !$rsImoveis->eof() ) {
                                //$this->obTCIMImovelCondominio->setDado ( 'cod_condominio', $this->inCodigoCondominio );
                                $this->obTCIMImovelCondominio->setDado( 'inscricao_municipal', $rsImoveis->getCampo ('inscricao_municipal'));
                                $obErro = $this->obTCIMImovelCondominio->inclusao ($boTransacao);
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }

                                $rsImoveis->proximo();
                            }
                        }

                        $cont++;
                    }
                }
            }
        }
      }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCondominio );

    return $obErro;
}

/**
* Altera as características do Condominio
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarCaracteristica($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //CADASTRO DE ATRIBUTOS
        $this->obTCIMCondominio->setDado( "cod_condominio", $this->inCodigoCondominio );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCIMCondominioProcesso->setDado( "cod_condominio"     , $this->inCodigoCondominio);
                $this->obTCIMCondominioProcesso->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCIMCondominioProcesso->setDado( "ano_exercicio"      , $this->obRProcesso->getExercicio()      );
                $this->obTCIMCondominioProcesso->inclusao( $boTransacao );
             }
            //O Restante dos valores vem setado da pÃ¡gina de processamento
            $arChaveAtributoCondominio = array( "cod_condominio" => $this->inCodigoCondominio );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCondominio );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCondominio );

    return $obErro;
}

/**
* Exclui informações do Condominio
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirCondominio($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMCondominioCgm->setDado( "cod_condominio" , $this->inCodigoCondominio  );
        $obErro = $this->obTCIMCondominioCgm->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMCondominioProcesso->setDado( "cod_condominio", $this->inCodigoCondominio );
            $obErro = $this->obTCIMCondominioProcesso->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMCondominioProcesso->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                $obErro = $this->obTCIMCondominioProcesso->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMCondominioAreaComum->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                    $obErro = $this->obTCIMCondominioAreaComum->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $arChaveAtributo = array( 'cod_condominio' => $this->inCodigoCondominio );
                        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->obTCIMLoteCondominio->setDado( "cod_condominio" , $this->inCodigoCondominio );
                            $obErro = $this->obTCIMLoteCondominio->exclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->obTCIMImovelCondominio->setDado( "cod_condominio" , $this->inCodigoCondominio );
                                $obErro = $this->obTCIMImovelCondominio->exclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $this->obTCIMCondominio->setDado( "cod_condominio" , $this->inCodigoCondominio  );
                                    $obErro = $this->obTCIMCondominio->exclusao( $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCondominio );

    return $obErro;
}

/**
* Lista os condomínios conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCondominios(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoCondominio) {
        $stFiltro .= " AND C.cod_condominio = ".$this->inCodigoCondominio;
    }
    if ($this->stNomCondominio) {
        $stFiltro .= " AND UPPER(C.nom_condominio) like UPPER('".$this->stNomCondominio."%')";
    }
    if ($this->inCodigoTipo <> "") {
        $stFiltro .= " AND C.cod_tipo = ".$this->inCodigoTipo;
    }
    if ( $this->obRCGM->getNumCgm() ) {
        $stFiltro .= " AND CC.numcgm = ".$this->obRCGM->getNumCgm();
    }
    if ( $this->obRCGM->getNomCgm() ) {
        $stFiltro .= " AND UPPER(CGM.nom_cgm) like UPPER('".$this->obRCGM->getNomCgm()."%')";
    }
    $stOrder = " ORDER BY C.cod_condominio ";
    $obErro = $this->obTCIMCondominio->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Lista os condomínios conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCondominiosLista(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoCondominio) {
        $stFiltro .= " AND C.cod_condominio = ".$this->inCodigoCondominio;
    }
    if ($this->stNomCondominio) {
        $stFiltro .= " AND UPPER(C.nom_condominio) like UPPER('".$this->stNomCondominio."%')";
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " AND C.cod_tipo = ".$this->inCodigoTipo;
    }
    if ( $this->obRCGM->getNumCgm() ) {
        $stFiltro .= " AND CC.numcgm = ".$this->obRCGM->getNumCgm();
    }
    $stOrder = " ORDER BY C.cod_condominio ";
    $obErro = $this->obTCIMCondominio->recuperaRelacionamentoLista( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
  * Lista os lotes pertencentes ao condomínio conforme o filtro setado
  * @access Public
  * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
  * @param  Object $obTransacao Parâmetro Transação
  * @return Object Objeto Erro
  */
function listarLotesCondominio(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoCondominio) {
        $stFiltro .= " AND cod_condominio = ".$this->inCodigoCondominio;
    }

    $stOrder = " ORDER BY cod_lote";
    $obErro = $this->obTCIMLoteCondominio->recuperaCondominioLotesLocalizacao ( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

function listarProcessos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoCondominio()) {
        $stFiltro .= " cp.cod_condominio = ".$this->getCodigoCondominio()." AND ";
    }
    if ( $this->getTimestampCondominio() ) {
        $stFiltro .= " cp.timestamp = '".$this->getTimestampCondominio()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cp.timestamp";
    $obErro = $this->obTCIMCondominio->recuperaRelacionamentoProcesso( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
* Verifica timestamp do condominio
* @access Public  coisas a maios
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function verificaTimestamp($boTransacao = "")
{
    $obErro = new Erro;
    $stFiltro  = " WHERE ";
    $stFiltro .= " cod_condominio = ".$this->inCodigoCondominio." AND " ;
    $stFiltro .= " timestamp = '".$this->tmTimestampCondominio."'" ;

    $obErro = $this->obTCIMCondominioAreaComum->recuperaTodos( $rsArea, $stFiltro, "", $boTransacao );

    return $obErro;
}

/**
  * Verifica Lote se já pertente a um condominio
  * @access Public
  * @param  Object $obTransacao Parâmetro Transação
  * @return Boolean
  */
function verificaLotePertenceCondominio($inCodigoLote, $inCodigoCondominio)
{
        $obErro = new Erro;
        $stFiltro  = " WHERE ";

        if ($inCodigoCondominio) {
            $stFiltro .= " cod_condominio <> ".$inCodigoCondominio ." AND " ;
        }
        $stFiltro .= " cod_lote = ". $inCodigoLote ;

        $obErro = $this->obTCIMLoteCondominio->recuperaTodos( $rsLista, $stFiltro, "", $boTransacao );

        if ( $rsLista->getNumLinhas() > 0 ) {
            return true;
        } else {
            return false;
        }

}

/**
* Consulta Comdominio de acordo com o filtro montado
* @access Public  coisas a maios
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarCondominio(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoCondominio) {
        $stFiltro .= " AND C.cod_condominio = ".$this->inCodigoCondominio;
    }
    if ($this->stNomCondominio) {
        $stFiltro .= " AND UPPER(C.nom_condominio) like UPPER('".$this->stNomCondominio."%')";
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " AND C.cod_tipo = ".$this->inCodigoTipo;
    }
    if ( $this->obRCGM->getNumCgm() ) {
        $stFiltro .= " AND CC.numcgm = ".$this->obRCGM->getNumCgm();
    }
    $stOrder = " ORDER BY C.cod_condominio ";
    $obErro = $this->obTCIMCondominio->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodigoCondominio   ( $rsRecordSet->getCampo('cod_condominio')   );
        $this->setCodigoTipo         ( $rsRecordSet->getCampo('cod_tipo')         );
        $this->setNomeTipo           ( $rsRecordSet->getCampo('nom_tipo')         );
        $this->setNomCondominio      ( $rsRecordSet->getCampo('nom_condominio')   );
        $this->setAreaTotalComum     ( $rsRecordSet->getCampo('area_total_comum') );
        $this->setTimestampCondominio( $rsRecordSet->getCampo('timestamp')        );
        $this->obRCGM->setNomCGM     ( $rsRecordSet->getCampo('nom_cgm')          );
        $this->obRCGM->setNumCGM     ( $rsRecordSet->getCampo('numcgm')           );
    }

    return $obErro;
}

/**
* Verifica se deve incluir, alterar ou excluir o processo informado
* @access Private
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarProcesso($boTransacao = "")
{
   $obErro = new Erro;
   if ( $this->obRProcesso->getCodigoProcesso() ) {
       $obErro = $this->obRProcesso->validarProcesso( $boTransacao );
   }
   if ( !$obErro->ocorreu() ) {
       $stFiltro  = " WHERE ";
       $stFiltro .= " cod_condominio = ".$this->inCodigoCondominio." ";
       $obErro = $this->obTCIMCondominioProcesso->recuperaTodos( $rsProcesso, $stFiltro, "", $boTransacao );
       if ( !$obErro->ocorreu() ) {
           $this->obTCIMCondominioProcesso->setDado( "cod_condominio" , $this->inCodigoCondominio               );
           $this->obTCIMCondominioProcesso->setDado( "cod_processo"   , $this->obRProcesso->getCodigoProcesso() );
           $this->obTCIMCondominioProcesso->setDado( "ano_exercicio"  , $this->obRProcesso->getExercicio()      );
            if ( $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
               $obErro = $this->obTCIMCondominioProcesso->inclusao( $boTransacao );
           } elseif ( !$rsProcesso->eof() ) {
               $this->obTCIMCondominioProcesso->setCampoCod( "cod_condominio" );
               $this->obTCIMCondominioProcesso->setComplementoChave( "" );
               $obErro = $this->obTCIMCondominioProcesso->exclusao( $boTransacao );
           }
       }
   }

   return $obErro;
}

/**
* Verifica se deve inclui uma nova area ou mantem a mesma. HISTORICO DE AREA COMUM
* @access Private
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarArea($boTransacao = "")
{
    $obErro = new Erro;
    $stFiltro  = " WHERE ";
    $stFiltro .= " cod_condominio = ".$this->inCodigoCondominio." AND ";
    $stFiltro .= " timestamp = ( select max(timestamp) FROM imobiliario.condominio_area_comum WHERE cod_condominio = ".$this->inCodigoCondominio." )";
    $this->obTCIMCondominioAreaComum->setDado( "cod_condominio"   , $this->inCodigoCondominio  );
    $this->obTCIMCondominioAreaComum->setDado( "area_total_comum" , $this->getAreaTotalComum() );
    $obErro = $this->obTCIMCondominioAreaComum->recuperaUltimaArea( $rsArea, $stFiltro, "", $boTransacao );
    $rsArea->addFormatacao( "area_total_comum", "NUMERIC_BR" );
    if ( !$obErro->ocorreu() and $rsArea->eof() ) {
        $obErro = $this->obTCIMCondominioAreaComum->inclusao( $boTransacao );
    } elseif ( !$obErro->ocorreu() and !$rsArea->eof() and $rsArea->getCampo('area_total_comum') != $this->getAreaTotalComum() ) {
        $obErro = $this->obTCIMCondominioAreaComum->inclusao( $boTransacao );
   }

   return $obErro;
}

/**
* Lista tipos de condomínio
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarTiposCondominio(&$rsTipoCondominio , $boTransacao = "")
{
    $obErro = $this->obTCIMTipoCondominio->recuperaTodos( $rsTipoCondominio, '', '', $boTransacao );

    return $obErro;
}

/**
* inclui reforma de condominio
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirReforma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMCondominioAreaComum->setDado( "cod_condominio"   , $this->inCodigoCondominio  );
        $this->obTCIMCondominioAreaComum->setDado( "area_total_comum" , $this->getAreaTotalComum() );
        $obErro = $this->obTCIMCondominioAreaComum->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() AND $this->obRProcesso->getCodigoProcesso() ) {
            $obErro = $this->salvarProcesso( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCondominio );

    return $obErro;
}

/**
* Verifica se o nome do condomínio a ser incluído já pertence à um condomínio cadastrado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function validaNomeCondominio(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro  = "";
    $stFiltro .= " AND UPPER(C.nom_condominio) like UPPER('".$this->stNomCondominio."%')";
    if ($this->inCodigoCondominio) {

        $stFiltro .= " AND C.cod_condominio <> ". $this->inCodigoCondominio ;
    }
    $obErro = $this->obTCIMCondominio->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
      * Adiciona um objeto de Lote
          * @access Public
          */
function addLote($arChaveLote)
{
    $this->arLote[count($this->arLote)] = $arChaveLote;

    return $obErro;

}

}
