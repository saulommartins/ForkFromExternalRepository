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
    * Classe de Regra de Negócio ConfiguracaoCEM
    * Data de Criação   : 23/11/2004

    * @author Analista : Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini

    * $Id: RCEMConfiguracao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.01
*/

/*
$Log$
Revision 1.12  2007/04/23 19:01:30  dibueno
retirado espaço vazio ao final do arquivo

Revision 1.11  2007/04/18 19:51:13  cercato
bug #9166#

Revision 1.10  2007/03/28 19:15:12  dibueno
Bug #8633#

Revision 1.9  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
//include_once    ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"         );

/**
    * Classe de Regra de Negócio ConfiguracaoCEM
    * Data de Criação   : 23/11/2004
    * @author Analista : Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
*/
class RCEMConfiguracao
{
/**
    * @var Object
    * @access Private
*/
var $obTAdministracaoConfiguracao;
/**
    * @var Object
    * @access Private
*/
var $obTAcao;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Integer
    * @access Private
*/
var $inNumeroLicenca;
/**
    * @var String
    * @access Private
*/
var $stMascaraLicenca;
/**
    * @var Boolean
    * @access Private
*/
var $boNumeroInscricao;
/**
    * @var String
    * @access Private
*/
var $stMascaraInscricao;
/**
    * @var Boolean
    * @access Private
*/
var $boCNAE;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoModulo;
/**
    * @var Integer
    * @access Private
*/
var $inAnoExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCGMDiretorTributos;

var $stVgSanitDepartamento;
var $stVgSanitSecretaria;
var $stEmissaoCertidaoBaixa;
var $stNroAlvara;

function setNroAlvara($valor) { $this->stNroAlvara = $valor; }
function getNroAlvara() { return $this->stNroAlvara; }
/**
    * @access Public
    * @param Object $valor
*/

function setTAdministracaoConfiguracao($valor) { $this->obTAdministracaoConfiguracao    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumeroLicenca($valor) { $this->inNumeroLicenca    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascaraLicenca($valor) { $this->stMascaraLicenca   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumeroInscricao($valor) { $this->boNumeroInscricao  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascaraInscricao($valor) { $this->stMascaraInscricao = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setEmissaoCertidaoBaixa($valor) { $this->stEmissaoCertidaoBaixa = $valor; }
function setCNAE($valor) { $this->boCNAE             = $valor; }
function setVgSanitDepartamento($valor) { $this->stVgSanitDepartamento  = $valor; }
function setVgSanitSecretaria($valor) { $this->stVgSanitSecretaria  = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setCGMDiretorTributos($valor) { $this->inCGMDiretorTributos  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodigoModulo($valor) { $this->inCodigoModulo     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAnoExercicio($valor) { $this->inAnoExercicio     = $valor; }
/**
    * @access Public
    * @return Object
*/
function getTAdministracaoConfiguracao() { return $this->obTAdministracaoConfiguracao;    }
/**
    * @access Public
    * @return Boolean
*/
function getNumeroLicenca() { return $this->inNumeroLicenca;    }
/**
    * @access Public
    * @return String
*/
function getMascaraLicenca() { return $this->stMascaraLicenca;   }
/**
    * @access Public
    * @return Boolean
*/
function getNumeroInscricao() { return $this->boNumeroInscricao;  }
/**
    * @access Public
    * @return String
*/
function getMascaraInscricao() { return $this->stMascaraInscricao; }
/**
    * @access Public
    * @return Boolean
*/
function getCNAE() { return $this->boCNAE;             }
/**
    * @access Public
    * @return Boolean
*/

function getEmissaoCertidaoBaixa() { return $this->stEmissaoCertidaoBaixa; }
function getCGMDiretorTributos() { return $this->inCGMDiretorTributos; }
function getVgSanitDepartamento() { return $this->stVgSanitDepartamento; }
function getVgSanitSecretaria() { return $this->stVgSanitSecretaria; }

/**
    * @access Public
    * @return
*/
function getCodigoModulo() { return $this->inCodigoModulo;     }
/**
    * @access Public
    * @return Object
*/
function getAnoExercicio() { return $this->inAnoExercicio;     }
/**
    * Método Construtor
    * @access Private
*/
function RCEMConfiguracao()
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
    include_once( CLA_TRANSACAO );
    $this->obTAdministracaoConfiguracao  = new TAdministracaoConfiguracao;
    $this->obTAcao          = new TAdministracaoAcao;
    $this->obTransacao      = new Transacao;
}

function verificaParametro(&$boExiste, $boTransacao = "")
{
    $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsConfiguracao->Eof() )
            $boExiste = false;
        else
            $boExiste = true;
    }

    return $obErro;
}

/**
    * Altera as configurações referentes ao cadastro econômico
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarConfiguracao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $obErro = $this->buscaModulo( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->inCodigoModulo );
    $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->inAnoExercicio );

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "numero_licenca" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->inNumeroLicenca );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_licenca" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->stMascaraLicenca );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "numero_inscricao_economica" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->boNumeroInscricao );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao_economica" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->stMascaraInscricao );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "cnae_fiscal" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->boCNAE );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "cnae_fiscal" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->boCNAE );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "diretor_tributos" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->inCGMDiretorTributos );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "diretor_tributos" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->inCGMDiretorTributos );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "sanit_departamento" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->stVgSanitDepartamento );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "sanit_secretaria" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->stVgSanitSecretaria );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "certidao_baixa" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->stEmissaoCertidaoBaixa );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "nro_alvara_licenca" );
    $this->obTAdministracaoConfiguracao->setDado( "valor"     , $this->stNroAlvara );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTAdministracaoConfiguracao );

    return $obErro;
}
/**
    * Recupera as configurações referentes ao cadastro economico
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarConfiguracao($boTransacao = "")
{
    $obErro = $this->buscaModulo( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", $this->inCodigoModulo );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , $this->inAnoExercicio );

        $this->obTAdministracaoConfiguracao->setDado( "parametro" , "numero_licenca" );
        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->inNumeroLicenca =  $rsConfiguracao->getCampo( "valor" );
            $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_licenca" );

            $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->stMascaraLicenca = $rsConfiguracao->getCampo( "valor" );
                $this->obTAdministracaoConfiguracao->setDado( "parametro" , "numero_inscricao_economica" );
                $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->boNumeroInscricao = $rsConfiguracao->getCampo( "valor" );

                    $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao_economica");
                    $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao);
                    if ( !$obErro->ocorreu() ) {
                       $this->stMascaraInscricao = $rsConfiguracao->getCampo( "valor" ) ;
                       $this->obTAdministracaoConfiguracao->setDado( "parametro" , "cnae_fiscal");
                       $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->boCNAE = $rsConfiguracao->getCampo( "valor" ) ;
                            $this->obTAdministracaoConfiguracao->setDado( "parametro", "diretor_tributos");
                            $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );

                            if ( !$obErro->ocorreu() ) {
                                $this->inCGMDiretorTributos = $rsConfiguracao->getCampo( "valor" ) ;
                                $this->obTAdministracaoConfiguracao->setDado( "parametro", "sanit_departamento");
                                $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $this->stVgSanitDepartamento = $rsConfiguracao->getCampo( "valor" );
                                    $this->obTAdministracaoConfiguracao->setDado( "parametro", "sanit_secretaria");
                                    $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $this->stVgSanitSecretaria = $rsConfiguracao->getCampo( "valor" );
                                        $this->obTAdministracaoConfiguracao->setDado( "parametro", "certidao_baixa");
                                        $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            $this->stEmissaoCertidaoBaixa = $rsConfiguracao->getCampo( "valor" );
                                            $this->obTAdministracaoConfiguracao->setDado( "parametro" , "nro_alvara_licenca" );
                                            $obErro = $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                $this->stNroAlvara = $rsConfiguracao->getCampo( "valor" );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $obErro;
}
/**
    * Recupera o codigo do modulo cadastro imobiliario
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function buscaModulo($boTransacao = "")
{
    $stFiltro  = " AND A.cod_acao = ".Sessao::read('acao')." ";
    $obErro = $this->obTAcao->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->inCodigoModulo =  $rsRelacionamento->getCampo("cod_modulo");
    }

    return $obErro;
}

/**
* Recupera do banco de dados as configurações do módulo setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function recuperaConfiguracao(&$rsConfiguracao, $boTransacao = "")
{
    $stFiltro = "";
    $stFiltro .= " cod_modulo = ".$this->inCodigoModulo." AND ";
    $stFiltro .= " exercicio = '".$this->inAnoExercicio."' AND ";
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $stOrder = " ORDER BY cod_modulo ";
    $obErro = $this->obTAdministracaoConfiguracao->recuperaTodos( $rsConfiguracao, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Recupera a Mascara de processo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarMascaraProcesso(&$stMascaraProcesso , $boTransacao = "")
{
    $stFiltro  = " WHERE COD_MODULO = 5 AND parametro = 'mascara_processo' ";
    $stFiltro .= " AND  exercicio = '".$this->getAnoExercicio()."' ";
    $stOrdem   = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTAdministracaoConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $stMascaraProcesso = $rsRecordSet->getCampo( "valor" );
    }

    return $obErro;
}

}
?>
