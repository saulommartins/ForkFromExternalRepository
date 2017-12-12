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
* Classe de negócio Norma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27553 $
$Name$
$Author: melo $
$Date: 2008-01-15 17:12:04 -0200 (Ter, 15 Jan 2008) $
$Id: RNorma.class.php 61604 2015-02-12 15:21:35Z evandro $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php"          );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"           );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaTipoNorma.class.php"  );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php");

class RNorma
{
/**
    * @access Private
    * @var Integer
*/
var $inCodNorma;
/**
    * @access Private
    * @var Numeric
*/
var $inNumNorma;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var String
*/
var $stNomeNorma;
/**
    * @access Private
    * @var String
*/
var $stDescricaoNorma;
/**
    * @access Private
    * @var String
*/
var $stDataPublicacao;
/**
    * @access Private
    * @var String
*/
var $stDataInicialPublicacao;
/**
    * @access Private
    * @var String
*/
var $stDataFinalPublicacao;
/**
    * @access Private
    * @var String
*/
var $stDataAssinatura;
/**
    * @access Private
    * @var String
*/
var $stDataTermino;
/**
    * @access Private
    * @var String
*/
var $stUrl;
/**
    * @access Private
    * @var String
*/
var $stLocalizacao;
/**
    * @access Private
    * @var Object
*/
var $obTNorma;
/**
    * @access Private
    * @var Object
*/
var $obTNormaTipoNorma;
/**
    * @access Private
    * @var Object
*/
var $obTNormaDataTermino;
/**
    * @access Private
    * @var Object
*/
var $obRTipoNorma;
/**
     * @access Private
     * @var Integer
*/
var $inCodLeiAlteracao;
/**
    * @access Private
    * @var String
*/
var $stNumNormaAlteracao;
/**
    * @access Private
    * @var String
*/
var $stDescricaoNormaAlteracao;
/**
    * @access Private
    * @var String
*/
var $inCodNormaAlteracao;
/**
    * @access Public
    * @param Integer $Valor
*/
var $stTipoBusca;
/**
    * @access Public
    * @param Numeric $Valor
*/
var $numPercentualCreditoAdicional;
/**
    * @access Public
    * @param Numeric $Valor
*/
var $inCodTipoLeiOrigemDecreto;
/**
    * @access Public
    * @param Numeric $Valor
*/
var $inCodTipoLeiAlteracaoOrcamentaria;

/**
    * @access Private
    * @var String
*/
function setCodNorma($valor) { $this->inCodNorma = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNumNorma($valor) { $this->inNumNorma = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomeNorma($valor) { $this->stNomeNorma = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricaoNorma($valor) { $this->stDescricaoNorma      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTipoBusca($valor) { $this->stTipoBusca      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDataPublicacao($valor) { $this->stDataPublicacao      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDataInicialPublicacao($valor) { $this->stDataInicialPublicacao      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDataFinalPublicacao($valor) { $this->stDataFinalPublicacao      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDataAssinatura($valor) { $this->stDataAssinatura      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDataInicialAssinatura($valor) { $this->stDataInicialAssinatura      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDataFinalAssinatura($valor) { $this->stDataFinalAssinatura = $valor; }
/**
    * @access Public
    * @param String $Valor
*/

function setDataTermino($valor) { $this->stDataTermino = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setUrl($valor) { $this->stUrl = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setLocalizacao($valor) { $this->stLocalizacao         = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao           = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTNorma($valor) { $this->obTNorma              = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTNormaTipoNorma($valor) { $this->obTNormaTipoNorma              = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTNormaDataTermino($valor) { $this->obTNormaDataTermino            = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRTipoNorma($valor) { $this->obRTipoNorma          = $valor; }
/**
    * @access Public
    * @return Integer
*/
function setNomeArquivo($valor) { $this->stNomeArquivo         = $valor; }
/**
    * @access Public
     * @param Integer $Valor
*/
function setCodLeiAlteracao($valor) { $this->inCodLeiAlteracao         = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNumNormaAlteracao($valor) { $this->stNumNormaAlteracao         = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricaoNormaAlteracao($valor) { $this->stDescricaoNormaAlteracao         = $valor; }
/**
    * @access Public
     * @param Integer $Valor
*/
function setCodNormaAlteracao($valor) { $this->inCodNormaAlteracao         = $valor; }
/**
    * @access Public
     * @param Numeric $Valor
*/
function setPercentualCreditoAdicional($valor) { $this->numPercentualCreditoAdicional = $valor; }
/**
    * @access Public
     * @param Numeric $Valor
*/
function setTipoLeiOrigemDecreto($valor) { $this->inCodTipoLeiOrigemDecreto= $valor; }
/**
    * @access Public
     * @param Numeric $Valor
*/
function setTipoLeiAlteracaoOrcamentaria($valor) { $this->inCodTipoLeiAlteracaoOrcamentaria = $valor; }
/**
    * @access Public
    * @return String
*/
function getCodNorma() { return $this->inCodNorma; }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
    * @access Public
    * @return String
*/
function getNumNorma() { return $this->inNumNorma; }
/**
    * @access Public
    * @return String
*/
function getNomeNorma() { return $this->stNomeNorma; }
/**
    * @access Public
    * @return String
*/
function getDescricaoNorma() { return $this->stDescricaoNorma; }
/**
    * @access Public
    * @return String
*/
function getTipoBusca() { return $this->stTipoBusca; }
/**
    * @access Public
    * @return String
*/
function getDataPublicacao() { return $this->stDataPublicacao      ; }
/**
    * @access Public
    * @return String
*/
function getDataInicialPublicacao() { return $this->stDataInicialPublicacao      ; }
/**
    * @access Public
    * @return String
*/
function getDataFinalPublicacao() { return $this->stDataFinalPublicacao      ; }
/**
    * @access Public
    * @return String
*/
function getDataAssinatura() { return $this->stDataAssinatura      ; }
/**
    * @access Public
    * @return String
*/
function getDataInicialAssinatura() { return $this->stDataInicialAssinatura      ; }
/**
    * @access Public
    * @return String
*/

function getDataFinalAssinatura() { return $this->stDataFinalAssinatura      ; }
/**
    * @access Public
    * @return String
*/

function getDataTermino() { return $this->stDataTermino      ; }
/**
    * @access Public
    * @return String
*/
function getUrl() { return $this->stUrl; }
/**
    * @access Public
    * @return String
*/
function getLocalizacao() { return $this->stLocalizacao         ; }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao           ; }
/**
    * @access Public
    * @return Object
*/
function getTNorma() { return $this->obTNorma              ; }
/**
    * @access Public
    * @return Object
*/
function getTNormaTipoNorma() { return $this->obTNormaTipoNorma              ; }
/**
    * @access Public
    * @return Object
*/
function getTNormaDataTermino() { return $this->obTNormaDataTermino          ; }
/**
    * @access Public
    * @return Object
*/
function getRTipoNorma() { return $this->obRTipoNorma          ; }
/**
    * @access Public
    * @return Object
*/
function getNomeArquivo() { return $this->stNomeArquivo		   ; }
/**
    * @access Public
     * @return Integer
*/
function getCodLeiAlteracao() { return $this->inCodLeiAlteracao      ; }
/**
    * @access Public
    * @return String 
*/
function getNumNormaAlteracao() { return $this->stNumNormaAlteracao   ; }
/**
    * @access Public
    * @return String
*/
function getDescricaoNormaAlteracao() { return $this->stDescricaoNormaAlteracao     ; }
/**
    * @access Public
    * @return Integer 
*/
function getCodNormaAlteracao() { return $this->inCodNormaAlteracao   ; }
/**
     * @access Public
     * @return Numeric 
*/
function getPercentualCreditoAdicional() { return $this->numPercentualCreditoAdicional   ; }
/**
    * @access Public
    * @return Numeric 
*/
function getTipoLeiOrigemDecreto() { return $this->inCodTipoLeiOrigemDecreto ; }
/**
    * @access Public
    * @return Numeric 
*/
function getTipoLeiAlteracaoOrcamentaria() { return $this->inCodTipoLeiAlteracaoOrcamentaria ; }

/**
     * Método construtor
     * @access Private
*/

function RNorma()
{
    $this->setTNorma       ( new TNorma       );
    $this->setTNormaTipoNorma   ( new TNormaTipoNorma       );
    $this->setTNormaDataTermino ( new TNormaDataTermino );
    $this->setTransacao    ( new Transacao    );
    $this->setRTipoNorma   ( new RTipoNorma   );
}

/**
    * Salva dados de Norma no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    unset($inFlagErro);
    
    if(intval($this->getCodNorma()) >= 0){
        $inFlagErro = SistemaLegado::pegaDado('cod_norma','normas.norma_data_termino'," where cod_norma =".intval($this->getCodNorma()));
    }

    $boFlagTransacao = false;
        
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        
    if ( !$obErro->ocorreu() ) {
        
        $inCodNorma = $this->getCodNorma();
               
        if (isset($inCodNorma)) {
            $this->obTNorma->setDado("dt_publicacao" , $this->getDataPublicacao() );
            $this->obTNorma->setDado("dt_assinatura" , $this->getDataAssinatura() );
            $this->obTNorma->setDado("nom_norma"     , $this->getNomeNorma() );
            $this->obTNorma->setDado("descricao"     , $this->getDescricaoNorma() );
            $this->obTNorma->setDado("link"          , $this->getNomeArquivo() );
            $this->obTNorma->setDado("exercicio"     , $this->getExercicio() );
            $this->obTNorma->setDado("num_norma"     , $this->getNumNorma() );
            $this->obTNorma->setDado("cod_tipo_norma", $this->obRTipoNorma->getCodTipoNorma() );
            
            $obErro = $this->validarNumeroNorma( $this->getCodNorma(), $boTransacao );
            
            if ( !$obErro->ocorreu() ) {
                $this->obTNorma->setDado("cod_norma", $this->getCodNorma() );
                $obErro = $this->obTNorma->alteracao( $boTransacao );
                
                if ( !$obErro->ocorreu()) {
                    if (isset($inFlagErro)) {
                       $this->obTNormaDataTermino->setDado("cod_norma", $this->getCodNorma() );
                       $this->obTNormaDataTermino->setDado("dt_termino", $this->getDataTermino() );
                       $obErro = $this->obTNormaDataTermino->alteracao( $boTransacao );
                    } else {
                        $this->obTNormaDataTermino->setDado("cod_norma", $this->getCodNorma() );
                        $this->obTNormaDataTermino->setDado("dt_termino", $this->getDataTermino() );
                        $obErro = $this->obTNormaDataTermino->inclusao( $boTransacao );
                    }
                }
            }
            
        } else {
            
            $obErro = $this->validarNumeroNorma( $this->getCodNorma(), $boTransacao );
            if ( !$obErro->ocorreu() ) {                
                $this->obTNorma->proximoCod( $inCodNorma , $boTransacao );                                
                $this->setCodNorma($inCodNorma);

                $this->obTNorma->setDado("cod_norma"     , $this->getCodNorma()         );
                $this->obTNorma->setDado("dt_publicacao" , $this->getDataPublicacao()   );
                $this->obTNorma->setDado("dt_assinatura" , $this->getDataAssinatura()   );
                $this->obTNorma->setDado("nom_norma"     , $this->getNomeNorma()        );
                $this->obTNorma->setDado("descricao"     , $this->getDescricaoNorma()   );
                $this->obTNorma->setDado("link"          , $this->getNomeArquivo()      );
                $this->obTNorma->setDado("exercicio"     , $this->getExercicio()        );
                $this->obTNorma->setDado("num_norma"     , $this->getNumNorma()         );
                $this->obTNorma->setDado("cod_tipo_norma", $this->obRTipoNorma->getCodTipoNorma() );
                $obErro = $this->obTNorma->inclusao( $boTransacao );
                
                if ( !$obErro->ocorreu() ) {
                     $this->obTNormaTipoNorma->setDado("cod_norma", $this->getCodNorma() );
                     $this->obTNormaTipoNorma->setDado("cod_tipo_norma", $this->obRTipoNorma->getCodTipoNorma() );
                     $obErro = $this->obTNormaTipoNorma->inclusao( $boTransacao );
                     if ( !$obErro->ocorreu() ) {
                        $this->obTNormaDataTermino->setDado("cod_norma", $this->getCodNorma() );
                        $this->obTNormaDataTermino->setDado("dt_termino", $this->getDataTermino() );
                        $obErro = $this->obTNormaDataTermino->inclusao( $boTransacao );
                    }
                }
            }
            
        }
        
        //-O codigo abaixo foi colocado para inserir uma figura no diretorio anexos com o código da norma.
        if ( !$obErro->ocorreu() ) {
            if ( $this->getUrl() ) {
                $stDestinoAnexo    = CAM_NORMAS.'anexos/';
                $stEnderecoArquivo = $this->getUrl();
                $stNomeArquivo	   = $this->getNomeArquivo();
                if (file_exists($stDestinoAnexo.$stNomeArquivo)) {
                    $obErro->setDescricao('Arquivo já existente, informe um arquivo com outro nome.');
                } else {
                    $boMoveArquivo = move_uploaded_file( $stEnderecoArquivo, $stDestinoAnexo.$stNomeArquivo);
                    if (!$boMoveArquivo) {
                        $obErro->setDescricao('Erro ao incluir arquivo.');
                    }
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
                $this->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo_norma" => $this->obRTipoNorma->getCodTipoNorma(), "cod_norma" => $this->getCodNorma() ) );
                $obErro = $this->obRTipoNorma->obRCadastroDinamico->salvarValores( $boTransacao );
        }
        
    }

    if ( !$obErro->ocorreu()) {
        switch (SistemaLegado::pegaConfiguracao( 'cod_uf', 2, Sessao::getExercicio(), $boTransacao )) {
            case 02: //TCEAL
            case 27: //TCETO
                if ($_REQUEST['stTipoLeiAlteracao']) {
                    $obErro = $this->salvarNormaAlterada($boTransacao);
                }
            break;
            case 11: //TCEMG
                $obErro = $this->salvarNormaAlterada($boTransacao);
            break;
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTNorma );

    return $obErro;
}

function salvarNormaAlterada($boTransacao)
{
    switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) ) {
        case 02: //TCEAL
            include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDetalheAl.class.php";
            $obNormaDetalheAl = new TNormaDetalheAl;
            $obNormaDetalheAl->setDado( 'cod_norma', $this->getCodNorma() );
            $obNormaDetalheAl->recuperaPorChave($rsNormaDetalhe, $boTransacao);
            
            if ($rsNormaDetalhe->getNumLinhas() > 0){
                $obNormaDetalheAl->setDado( 'cod_lei_alteracao'  , $this->getCodLeiAlteracao()       );
                $obNormaDetalheAl->setDado( 'cod_norma_alteracao', $this->getCodNormaAlteracao()     );
                $obNormaDetalheAl->setDado( 'descricao_alteracao', $this->getDescricaoNormaAlteracao() );
                $obErro = $obNormaDetalheAl->alteracao($boTransacao);                
            } else {
                $obNormaDetalheAl->setDado( 'cod_lei_alteracao'  , $this->getCodLeiAlteracao()       );
                $obNormaDetalheAl->setDado( 'cod_norma_alteracao', $this->getCodNormaAlteracao()     );
                $obNormaDetalheAl->setDado( 'descricao_alteracao', $this->getDescricaoNormaAlteracao() );
                $obErro = $obNormaDetalheAl->inclusao($boTransacao);
            }
        break;
        
        case 27: //TCETO
            include_once ( CAM_GPC_TCETO_MAPEAMENTO."TTCETONormaDetalhe.class.php"  );
            $obTTCETONormaDetalhe = new TTCETONormaDetalhe;
            $obTTCETONormaDetalhe->setDado( 'cod_norma', $this->getCodNorma() );
            
            $obTTCETONormaDetalhe->recuperaPorChave($rsNormaDetalhe, $boTransacao);
            
            if ($rsNormaDetalhe->getNumLinhas() > 0){
                $obTTCETONormaDetalhe->setDado( 'cod_lei_alteracao'           , $this->getCodLeiAlteracao()            );
                $obTTCETONormaDetalhe->setDado( 'cod_norma_alteracao'         , $this->getCodNormaAlteracao()          );
                $obTTCETONormaDetalhe->setDado( 'percentual_credito_adicional', $this->getPercentualCreditoAdicional() );
                $obErro = $obTTCETONormaDetalhe->alteracao($boTransacao);
            } else {
                $obTTCETONormaDetalhe->setDado( 'cod_lei_alteracao'           , $this->getCodLeiAlteracao()            );
                $obTTCETONormaDetalhe->setDado( 'cod_norma_alteracao'         , $this->getCodNormaAlteracao()          );
                $obTTCETONormaDetalhe->setDado( 'percentual_credito_adicional', $this->getPercentualCreditoAdicional() );
                $obErro = $obTTCETONormaDetalhe->inclusao($boTransacao);
            }
        break;
    
        case 11://TCEMG
            include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNormaDetalhe.class.php"  );
            $obTTCEMGNormaDetalhe = new TTCEMGNormaDetalhe;
            $obTTCEMGNormaDetalhe->setDado( 'cod_norma', $this->getCodNorma() );
            $obTTCEMGNormaDetalhe->recuperaPorChave($rsNormaDetalhe, $boTransacao);
          
            if ($rsNormaDetalhe->getNumLinhas() > 0){
                $obTTCEMGNormaDetalhe->setDado( 'tipo_lei_origem_decreto'             , $this->getTipoLeiOrigemDecreto()         );
                if($_REQUEST['stTipoLeiAlteracaoOrcamentaria']){
                    $obTTCEMGNormaDetalhe->setDado( 'tipo_lei_alteracao_orcamentaria' , $this->getTipoLeiAlteracaoOrcamentaria() );
                }
                $obErro = $obTTCEMGNormaDetalhe->exclusao($boTransacao);
                if( $this->getTipoLeiOrigemDecreto() ){
                    $obErro = $obTTCEMGNormaDetalhe->inclusao($boTransacao);
                }
            } else {
                $obTTCEMGNormaDetalhe->setDado( 'tipo_lei_origem_decreto'             , $this->getTipoLeiOrigemDecreto()         );
                if($_REQUEST['stTipoLeiAlteracaoOrcamentaria']){
                    $obTTCEMGNormaDetalhe->setDado( 'tipo_lei_alteracao_orcamentaria' , $this->getTipoLeiAlteracaoOrcamentaria() );
                }
                $obErro = $obTTCEMGNormaDetalhe->inclusao($boTransacao);
            }
            
        break;
    }
    
    return $obErro;
}

/**
    * Verifica se já existe algum número de norma cadastrado para um tipo de norma e exercício selecionados
    * @access Public
    * @param  Integer $inCodNorma Código na norma para o caso se alteração
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validarNumeroNorma($inCodNorma , $boTransacao)
{
    $stFiltro = " WHERE \n";
    if ( isset($inCodNorma) ) {
        $stFiltro .= " cod_norma <> ".$inCodNorma." AND \n ";
    }
    $stFiltro .= " exercicio = '".$this->getExercicio()."' AND \n";
    $stFiltro .= " cod_tipo_norma = ".$this->obRTipoNorma->getCodTipoNorma()." AND \n";
    $stFiltro .= " num_norma = '".$this->getNumNorma()."' \n";
    $obErro = $this->obTNorma->recuperaTodos( $rsNorma, $stFiltro, '', $boTransacao );
    
    if ( !$obErro->ocorreu() ) {
        if ( !$rsNorma->eof() ) {
            $obErro->setDescricao('O número informado já existe para o tipo de norma e exercício selecionados!');
        }
    }

    return $obErro;
}

/**
    * Exclui dados de norma do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    
    if ( !$obErro->ocorreu() ) {
        $this->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo_norma" => $this->obRTipoNorma->getCodTipoNorma(), "cod_norma" => $this->getCodNorma() ) );
        $obErro = $this->obRTipoNorma->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTNormaTipoNorma->setDado("cod_norma", $this->getCodNorma() );
            $this->obTNormaTipoNorma->setDado("cod_tipo_norma", $this->obRTipoNorma->getCodTipoNorma() );
            $obErro = $this->obTNormaTipoNorma->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTNormaDataTermino->setDado("cod_norma", $this->getCodNorma() );
                $obErro = $this->obTNormaDataTermino->exclusao( $boTransacao );
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao)==11) { 
                    include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNormaDetalhe.class.php" );
                    $obTTCEMGNormaDetalhe = new TTCEMGNormaDetalhe;
                    $obTTCEMGNormaDetalhe->setDado( 'cod_norma' , $this->getCodNorma() );
                    $obTTCEMGNormaDetalhe->recuperaPorChave($rsNormaDetalhe, $boTransacao);
                    if($rsNormaDetalhe->getNumLinhas()>0){
                      $obTTCEMGNormaDetalhe->exclusao($boTransacao);  
                    }
                }
                if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao)==27) { 
                    include_once ( CAM_GPC_TCETO_MAPEAMENTO."TTCETONormaDetalhe.class.php" );
                    $obTTCETONormaDetalhe = new TTCETONormaDetalhe();
                    $obTTCETONormaDetalhe->setDado( 'cod_norma' , $this->getCodNorma() );
                    $obTTCETONormaDetalhe->recuperaPorChave($rsNormaDetalhe, $boTransacao);
                    if($rsNormaDetalhe->getNumLinhas()>0){
                      $obTTCETONormaDetalhe->exclusao($boTransacao);  
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    $this->obTNorma->setDado("cod_norma", $this->getCodNorma() );
                    $obErro = $this->obTNorma->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $stNomeArquivo  = $this->getNomeArquivo();
                        $stDestinoAnexo = CAM_NORMAS.'anexos/';
                        if ($stNomeArquivo != '') {
                            unlink( $stDestinoAnexo.$stNomeArquivo );
                        }
                    }
                }
            }
        }
    }
    
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTNorma );

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
function listar(&$rsRecordSet, $boTransacao = "")
{
    $obErro = new Erro;
    $stFiltro = " WHERE cod_norma IS NOT NULL ";

    if( $this->getDataPublicacao() )
        $stFiltro .= " AND dt_publicacao = to_date('".$this->getDataPublicacao()."', 'dd/mm/yyyy') ";
    if( $this->getCodNorma() != "" )
        $stFiltro .= " AND cod_norma = ".$this->getCodNorma()." ";
    if ( $this->getDataInicialPublicacao() && $this->getDataFinalPublicacao() ) {
        $stFiltro .= " AND dt_publicacao between to_date('".$this->getDataInicialPublicacao()."', 'dd/mm/yyyy')";
        $stFiltro .= " AND to_date( '".$this->getDataFinalPublicacao()."', 'dd/mm/yyyy') ";
    }
    if( $this->getNomeNorma() ){
        switch ($this->getTipoBusca()) {
            case 'inicio':
                $stFiltro .= " AND upper(nom_norma)  like upper('".$this->getNomeNorma()."%') ";
            break;
            case 'final':
                $stFiltro .= " AND upper(nom_norma)  like upper('%".$this->getNomeNorma()."') ";
            break;
            case 'contem':
                $stFiltro .= " AND upper(nom_norma)  like upper('%".$this->getNomeNorma()."%') ";
            break;
            case 'exata':
                $stFiltro .= " AND upper(nom_norma)  like upper('".$this->getNomeNorma()."') ";
            break;
            //Evitando conflito com outros fontes que usam esse metodo
            default:
                $stFiltro .= " AND upper(nom_norma)  like upper('%".$this->getNomeNorma()."%') ";
            break;
        }
    }
    if( $this->getDescricaoNorma() )
        $stFiltro .= " AND upper(descricao)  like upper('%".$this->getDescricaoNorma()."%') ";
    if( $this->stUrl )
        $stFiltro .= " AND link = '".$this->getUrl()."' ";
    if( $this->getLocalizacao() )
        $stFiltro .= " AND localizacao  like '%".$this->getLocalizacao()."%' ";
    if( $this->obRTipoNorma->getCodTipoNorma() <> '' )
        $stFiltro .= " AND cod_tipo_norma = ".$this->obRTipoNorma->getCodTipoNorma()." ";
    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";
    if( $this->getNumNorma() )
        $stFiltro .= " AND num_norma = LTRIM('".$this->getNumNorma()."'".","."'0')";
    $stOrder = " ORDER BY N.num_norma,N.exercicio ";
    $obErro = $this->obTNorma->recuperaNormas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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

function listarGenerico(&$rsRecordSet, $boTransacao = "", $stCase = "")
{
    $stFiltro = " WHERE cod_norma IS NOT NULL ".$stFiltro;

    if( $this->getDataPublicacao() )
        $stFiltro .= " AND dt_publicacao = to_date(".$this->getDataPublicacao().", 'dd/mm/yyyy') ";
    if( $this->getCodNorma() != "" )
        $stFiltro .= " AND cod_norma = ".$this->getCodNorma()." ";
    if ( $this->getDataInicialPublicacao() && $this->getDataFinalPublicacao() ) {
        $stFiltro .= " AND dt_publicacao between to_date('".$this->getDataInicialPublicacao()."', 'dd/mm/yyyy')";
        $stFiltro .= " AND to_date( '".$this->getDataFinalPublicacao()."', 'dd/mm/yyyy') ";
    }
    if ( $this->getDataInicialAssinatura() && $this->getDataFinalAssinatura() ) {
        $stFiltro .= " AND dt_assinatura between to_date('".$this->getDataInicialAssinatura()."', 'dd/mm/yyyy')";
        $stFiltro .= " AND to_date( '".$this->getDataFinalAssinatura()."', 'dd/mm/yyyy') ";
    }
    if( $this->getNomeNorma() )
        $stFiltro .= " AND upper(nom_norma)  like upper('%".$this->getNomeNorma()."%') ";
    if( $this->getDescricaoNorma() )
        $stFiltro .= " AND upper(descricao)  like upper('%".$this->getDescricaoNorma()."%') ";
    if( $this->stUrl )
        $stFiltro .= " AND link = '".$this->getUrl()."' ";
    if( $this->getLocalizacao() )
        $stFiltro .= " AND localizacao  like '%".$this->getLocalizacao()."%' ";
    if( $this->obRTipoNorma->getCodTipoNorma() <> '' )
        $stFiltro .= " AND cod_tipo_norma = ".$this->obRTipoNorma->getCodTipoNorma()." ";
    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";
    if( $this->getNumNorma() )
        $stFiltro .= " AND num_norma = '".$this->getNumNorma()."'";

    switch ($stCase) {
        case "vigente":
            $stFiltro .= " AND (dt_termino is NULL OR dt_termino > '".date("Y-m-d")."'".")";
            break;
        case "revogada":
            $stFiltro .= " AND dt_termino < '".date("Y-m-d")."'";
            break;
        case "vigente_ate":
            if ( $this->getDataTermino() ) {
                $stFiltro .= " AND dt_termino is NOT NULL";
                $stFiltro .= " AND dt_termino <= '".SistemaLegado::dataToSql($this->getDataTermino())."' ";
            }
            break;
    }

    $stOrder = ($stOrder)?$stOrder:" ORDER BY N.num_norma,N.exercicio ";
    $obErro = $this->obTNorma->recuperaNormasVigenteOrRevogado( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarDecreto(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = " WHERE cod_norma IS NOT NULL ";

    if( $this->getDataPublicacao() )
        $stFiltro .= " AND N.dt_publicacao = to_date('dd/mm/yyyy',".$this->getDataPublicacao().") ";
    if( $this->getCodNorma() )
        $stFiltro .= " AND N.cod_norma = ".$this->getCodNorma()." ";
    if( $this->getDataInicialPublicacao() && $this->getDataFinalPublicacao() )
        $stFiltro .= " AND N.dt_publicacao between '".$this->getDataInicialPublicacao()."' and '".$this->getDataFinalPublicacao()."'";
    if( $this->getNomeNorma() )
        $stFiltro .= " AND upper(N.nom_norma)  like upper('%".$this->getNomeNorma()."%') ";
    if( $this->getDescricaoNorma() )
        $stFiltro .= " AND upper(N.descricao)  like upper('%".$this->getDescricaoNorma()."%') ";
    if( $this->stUrl )
        $stFiltro .= " AND link = '".$this->getUrl()."' ";
    if( $this->getLocalizacao() )
        $stFiltro .= " AND upper(localizacao)  like upper('%".$this->getLocalizacao()."%') ";
    if( $this->obRTipoNorma->getCodTipoNorma() != "" )
        $stFiltro .= " AND N.cod_tipo_norma = ".$this->obRTipoNorma->getCodTipoNorma()." ";
    if( $this->stExercicio )
        $stFiltro .= " AND N.exercicio = '".$this->stExercicio."' ";
    if(!isset($stOrder))
        $stOrder = " ORDER BY N.num_norma,N.exercicio ";
    $obErro = $this->obTNorma->recuperaNormasDecreto( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
    $this->obTNorma->setDado( "cod_norma" , $this->getCodNorma() );
    $obErro = $this->obTNorma->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNumNorma         ( $rsRecordSet->getCampo("num_norma") );
        $this->setExercicio        ( $rsRecordSet->getCampo("exercicio") );
        $this->setDataPublicacao   ( $rsRecordSet->getCampo("dt_publicacao") );
        $this->setDataAssinatura   ( $rsRecordSet->getCampo("dt_assinatura") );
        $this->setNomeNorma        ( $rsRecordSet->getCampo("nom_norma") );
        $this->setDescricaoNorma   ( $rsRecordSet->getCampo("descricao") );
        $this->setUrl              ( $rsRecordSet->getCampo("link") );
        $this->obRTipoNorma->setCodTipoNorma( $rsRecordSet->getCampo("cod_tipo_norma") );
        $obErro = $this->obRTipoNorma->consultar( $rsRecordSet2 , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRTipoNorma->setNomeTipoNorma( $rsRecordSet2->getCampo( 'nom_tipo_norma' ) );
            $this->obTNormaDataTermino->setDado( "cod_norma" , $this->getCodNorma() );
            $obErro = $this->obTNormaDataTermino->recuperaPorChave( $rsRecordSet3, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->setDataTermino      ( $rsRecordSet3->getCampo("dt_termino") );
            }

        }
    }

    return $obErro;
}

}
