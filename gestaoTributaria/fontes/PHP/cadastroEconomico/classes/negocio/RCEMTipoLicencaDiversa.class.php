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
    * Classe de regra de negócio para TipoLicencaDiversa
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMTipoLicencaDiversa.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.11
*/

/*
$Log$
Revision 1.9  2007/05/14 20:39:08  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.8  2007/05/11 20:29:17  dibueno
Alterações para possibilitar a emissao do alvará

Revision 1.7  2007/02/27 14:50:57  cassiano
Bug #8435#

Revision 1.6  2006/10/11 10:28:57  dibueno
Atualizações para utilização da tabela tipo_licenca_modelo_documento

Revision 1.5  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                         );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"                     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMTipoLicencaDiversa.class.php"          );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoTipoLicencaDiversa.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoLicencaDiversaValor.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoCadastroEconomico.class.php"   );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMTipoLicencaModeloDocumento.class.php" );

class RCEMTipoLicencaDiversa
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipoLicencaDiversa;
/**
    * @access Private
    * @var String
*/
var $roUltimoElemento;
/**
    * @access Private
    * @var Object
*/

var $stNomeTipoLicencaDiversa;

/**
    * @access Private
    * @var Object
*/
//var $roUltimoElemento;

var $inTipoUtilizacao;

function setRCadastroDinamico($valor) { $this->obRCadastroDinamico   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipoLicencaDiversa($valor) { $this->inCodigoTipoLicencaDiversa = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/

function setNomeTipoLicencaDiversa($valor) { $this->stNomeTipoLicencaDiversa = $valor;   }

/**
    * @access Public
    * @return Integer
*/

function setTipoUtilizacao($valor) {$this->inTipoUtilizacao = $valor; }
function getRCadastroDinamico() { return $this->obRCadastroDinamico;   }

/**
    * @access Public
    * @return Integer
*/

function getCodigoTipoLicencaDiversa() { return $this->inCodigoTipoLicencaDiversa; }
/**
    * @access Public
    * @return String
*/
function getNomeTipoLicencaDiversa() { return $this->stNomeTipoLicencaDiversa;   }

/**
    * Método construtor
    * @access Private
*/
function RCEMTipoLicencaDiversa()
{
    $this->obTCEMTipoLicencaDiversa  = new TCEMTipoLicencaDiversa;
    $this->obTCEMTipoLicencaModeloDocumento = new TCEMTipoLicencaModeloDocumento;
    $this->obTransacao               = new Transacao;
    $this->obRCEMConfiguracao        = new RCEMConfiguracao;
    $this->arRCEMElemento            = array();
    $this->obRCadastroDinamico       = new RCadastroDinamico;
    //$this->obRCadastroDinamico->setPersistenteValores( new TCEMAtributoTipoLicencaDiversa );
    //$this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa );
    $this->obRCadastroDinamico->setCodCadastro( 4 );
}
/**
    * Adiciona um objeto de elemento no de Tipo de licenca diversa
    * @access Public
*/

function addTipoLicencaDiversaElemento()
{
    $this->arRCEMElemento[] = new RCEMElemento( $this );
    $this->roUltimoElemento = &$this->arRCEMElemento[ count($this->arRCEMElemento) - 1 ];
}

/**
    * inclui dados do Tipo de licença no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function incluirTipoLicencaDiversa($boTransacao = "")
{
;
$boFlaTransacao = false;
$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMTipoLicencaDiversa->proximoCod ( $this->inCodigoTipoLicencaDiversa, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMTipoLicencaDiversa->setDado( "cod_tipo", $this->inCodigoTipoLicencaDiversa );
            $this->obTCEMTipoLicencaDiversa->setDado( "nom_tipo" , $this->stNomeTipoLicencaDiversa );
            $this->obTCEMTipoLicencaDiversa->setDado( "cod_utilizacao", $this->inTipoUtilizacao );
            $obErro = $this->validaNomeTipoLicencaDiversa ($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTCEMTipoLicencaDiversa->inclusao( $boTransacao );
                //CADASTRA OS ELEMENTOS NA TABELA ECONOMICO.ELEMENTO_TIPO_LICENCA_DIVERSA
                if ( !$obErro->ocorreu() ) {
                    if ( count($this->roUltimoElemento) ) {
                        foreach ($this->arRCEMElemento as $obRCEMElemento) {
                            $obRCEMElemento->referenciaTipoLicencaDiversa($this);
                            $obErro = $obRCEMElemento->incluirTipoLicencaDiversaElemento( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }
                    //CADASTRA OS ATRIBUTOS NA TABELA ECONOMICO.ATRIBUTO_TIPO_LICENCA_DIVERSA
                    if ( !$obErro->ocorreu() ) {
                        $this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa() );
                        //O Restante dos valores vem setado da página de processamento
                        $arChaveAtributo =  array( "cod_tipo" => $this->inCodigoTipoLicencaDiversa );
                        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                        $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                    }

                    //CADASTRA OS REGISTROS NA TABELA ECONOMICO.TIPO_LICENCA_MODELO_DOCUMENTO
                    $arDocumentosSessao = Sessao::read( "documentos" );
                    $inRegistros = count ( $arDocumentosSessao );
                    if ( !$obErro->ocorreu() ) {
                        $inX = 0;
                        while ( $inX < $inRegistros && !$obErro->ocorreu() ) {

                            $inCodTipoDocumentoAtual = $arDocumentosSessao[$inX]['cod_tipo_documento'];
                            $inCodDocumentoAtual = $arDocumentosSessao[$inX]['cod_documento'];

                            $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo', $this->inCodigoTipoLicencaDiversa );
                            $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_utilizacao', $this->inTipoUtilizacao );
                            $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo_documento', $inCodTipoDocumentoAtual );
                            $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_documento', $inCodDocumentoAtual);
                            $obErro = $this->obTCEMTipoLicencaModeloDocumento->inclusao ( $boTransacao );
                            //$this->obTCEMTipoLicencaModeloDocumento->debug();

                            $inX++;
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMTipoLicencaDiversa );

    return $obErro;
}

/**
    * altera dados do Tipo de Licença no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterarTipoLicencaDiversa($boTransacao = "")
{
    $boFlaTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMTipoLicencaDiversa->setDado( "cod_tipo" , $this->inCodigoTipoLicencaDiversa );
        $this->obTCEMTipoLicencaDiversa->setDado( "nom_tipo" , $this->stNomeTipoLicencaDiversa );
        $obErro = $this->validaNomeTipoLicencaDiversa ($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTCEMTipoLicencaDiversa->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                //O Restante dos valores vem setado da página de processamento
                $this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa() );
                $arChaveAtributo =  array( "cod_tipo" => $this->inCodigoTipoLicencaDiversa );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    if ( count( $this->arRCEMElemento ) ) {
                        $obErro = $this->alterarTipoLicencaDiversaElemento( $boTransacao );
                    } else {
                        $this->addTipoLicencaDiversaElemento();
                        $obErro = $this->roUltimoElemento->excluirElementoTipoLicencaDiversa( $boTransacao);
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo', $this->inCodigoTipoLicencaDiversa );
                $obErro = $this->obTCEMTipoLicencaModeloDocumento->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $arDocumentosSessao = Sessao::read( "documentos" );
                    $inRegistros = count ( $arDocumentosSessao );
                    $inX = 0;
                    while ( $inX < $inRegistros && !$obErro->ocorreu() ) {
                        $inCodTipoDocumentoAtual = $arDocumentosSessao[$inX]['cod_tipo_documento'];
                        $inCodDocumentoAtual = $arDocumentosSessao[$inX]['cod_documento'];

                        $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo', $this->inCodigoTipoLicencaDiversa );
                        $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo_documento', $inCodTipoDocumentoAtual );
                        $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_documento', $inCodDocumentoAtual);
                        $obErro = $this->obTCEMTipoLicencaModeloDocumento->inclusao ( $boTransacao );
                        #$this->obTCEMTipoLicencaModeloDocumento->debug();

                        $inX++;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMTipoLicencaDiversa );

    return $obErro;
}

/**
    * Excluir uma Categoria
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirTipoLicencaDiversa($boTransacao = "")
{
    $boFlaTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( !$obErro->ocorreu() ) {
            $this->addTipoLicencaDiversaElemento();
            $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->recuperaPorChave($rsElementoTipoLicencaDiversa);
            if ($rsElementoTipoLicencaDiversa->getNumLinhas() > 0) {
                $obErro->setDescricao("Tipo de licença ".$this->inCodigoTipoLicencaDiversa." - ".$this->stNomeTipoLicencaDiversa." ainda está sendo referenciada em Elemento Tipo Licença Diversa!");
            }
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo', $this->inCodigoTipoLicencaDiversa );
            $this->obTCEMTipoLicencaModeloDocumento->recuperaPorChave($rsTipoLicencaModeloDocumento);
            if ($rsTipoLicencaModeloDocumento->getNumLinhas() > 0) {
                $obErro->setDescricao("Tipo de licença ".$this->inCodigoTipoLicencaDiversa." - ".$this->stNomeTipoLicencaDiversa." ainda está sendo referenciada em Tipo de Licença Modelo Documento!");
            }
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTCEMTipoLicencaDiversa->setDado( "cod_tipo", $this->inCodigoTipoLicencaDiversa );
            $this->obTCEMTipoLicencaDiversa->setDado( "nom_tipo" , $this->stNomeTipoLicencaDiversa );

            $this->obTCEMTipoLicencaDiversa->recuperaPorChave($rsTipoLicencaDiversa);
            if ($rsTipoLicencaDiversa->getNumLinhas() > 0) {
                $obErro->setDescricao("Tipo de licença ".$this->inCodigoTipoLicencaDiversa." - ".$this->stNomeTipoLicencaDiversa." ainda está sendo referenciada em Tipo de Licença Diversa!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            $this->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa() );
            $arChaveAtributo =  array( "cod_tipo" => $this->inCodigoTipoLicencaDiversa );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
            $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMTipoLicencaDiversa );

    return $obErro;
}

/**
  * Altera os elementos para tipo de licença
  * @access Public
  * @param  Object $obTransacao Parâmetro Transação
  * @return Object Objeto Erro
*/
function alterarTipoLicencaDiversaElemento($boFlagTransacao, $boTransacao = "")
{
    $boFlaTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $stFiltro = " AND A.cod_tipo = ".$this->inCodigoTipoLicencaDiversa;
        $obErro = $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->recuperaElementoTipoLicencaDiversa( $rsElementoTipoLicencaDiversa, $stFiltro, '', $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $arElementoTipoLicencaDiversa = array();
            while ( !$rsElementoTipoLicencaDiversa->eof() ) {
                $inCodigoElemento = $rsElementoTipoLicencaDiversa->getCampo( "cod_elemento" );
                $arElementoTipoLicencaDiversa[$inCodigoElemento] = true;
                $rsElementoTipoLicencaDiversa->proximo();
            }
            foreach ($this->arRCEMElemento as $obRCEMElemento) {
                if ( !isset( $arElementoTipoLicencaDiversa[$obRCEMElemento->getCodigoElemento()] ) ) {
                    $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->setDado( "cod_tipo",$this->inCodigoTipoLicencaDiversa );
                    $obRCEMElemento->referenciaTipoLicencaDiversa($this);
                    $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->setDado("cod_elemento",  $obRCEMElemento->getCodigoElemento());
                    $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->setDado("ativo",  true );
                    $obErro =$this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->inclusao( $boTransacao );
                    #$this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->debug();

                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                } else {
                    unset( $arElementoTipoLicencaDiversa[$obRCEMElemento->getCodigoElemento()] );
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($arElementoTipoLicencaDiversa as $inCodigoElemento => $boValor) {
                    $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->setDado( "cod_tipo", $this->inCodigoTipoLicencaDiversa );
                    $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->setDado( "cod_elemento",  $inCodigoElemento);
                    $obErro = $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa->exclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    //O Restante dos valores vem setado da página de processamento
                    $arChaveAtributo =  array( "cod_tipo" => $this->inCodigoTipoLicencaDiversa );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                    $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->roUltimoElemento->obTCEMElementoTipoLicencaDiversa );

    return $obErro;
}

/**
    * Lista os tipos de licenças diversas.
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function listarTipoLicencaDiversa(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoTipoLicencaDiversa() ) {
        $stFiltro .= "etld.cod_tipo = ".$this->getCodigoTipoLicencaDiversa()." AND ";
    }
    if ( $this->getNomeTipoLicencaDiversa() ) {
        $stFiltro .= "UPPER(nom_tipo ) like UPPER( '%".$this->getNomeTipoLicencaDiversa()."%' ) AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrdem = " ORDER BY etld.cod_tipo";
    $obErro = $this->obTCEMTipoLicencaDiversa->recuperaTipoLicencaDiversa( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
#	$this->obTCEMTipoLicencaDiversa->debug();   #exit;

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function consultar(&$rsRecordSet, $boTransacao = "")
{
    $this->obTCEMTipoLicencaDiversa->setDado("cod_tipo", $this->getCodigoTipoLicencaDiversa() );

    $obErro = $this->obTCEMTipoLicencaDiversa->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->setNomeTipoLicencaDiversa( $rsRecordSet->getCampo("nom_tipo") );
    }

    return $obErro;
}

function validaNomeTipoLicencaDiversa($boTransacao = "")
{
    $stFiltro = " WHERE  nom_tipo = '".$this->stNomeTipoLicencaDiversa."' ";
    if ($this->inCodigoTipoLicencaDiversa AND $this->stNomeTipoLicencaDiversa) {
        $stFiltro .= " AND cod_tipo <> ".$this->inCodigoTipoLicencaDiversa;
    }
    $stOrdem = "";
    $obErro = $this->obTCEMTipoLicencaDiversa->recuperaTodos( $rsLicenca, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsLicenca->eof() ) {
        $obErro->setDescricao( "Já existe outro  tipo de licenca cadastrado com o nome ".$this->stNomeTipoLicencaDiversa."! " );
    }

    return $obErro;
}

function recuperaTipoModeloDocumento(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = '';

    if ($this->inCodigoTipoLicencaDiversa) {
        $stFiltro = " WHERE cod_tipo = ". $this->inCodigoTipoLicencaDiversa ;
    }

    $stOrdem = "\n ORDER BY timestamp DESC \n LIMIT 1 ";

    $obErro = $this->obTCEMTipoLicencaModeloDocumento->recuperaLicencaDiversaModeloDocumento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;

}

}
