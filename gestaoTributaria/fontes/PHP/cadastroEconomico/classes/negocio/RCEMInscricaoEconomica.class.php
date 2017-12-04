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
    * Classe de regra de negócio para Inscricao Economica
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMInscricaoEconomica.class.php 63376 2015-08-21 18:55:42Z arthur $

    * Casos de uso: uc-05.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php"           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroAtributo.class.php"            );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioFiscal.class.php"             );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioInformado.class.php"          );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconRespContabil.class.php"    );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconRespTecnico.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMBaixaCadastroEconomico.class.php"      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMTipoBaixaInscricao.class.php"      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoAtivCadEconomico.class.php"    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoDiasSemana.class.php"        );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMDiasCadastroEconomico.class.php"       );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconRespTecnico.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoAtivCadEconomico.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"             );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                               );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"                   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"             );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaAtividade.class.php"            );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMSociedade.class.php"             );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMDomicilio.class.php"                      );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                  );
//ADICIONADOS PARA SEREM UTILIZADOS NA CONSULTA
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"                      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"               );
//adicionados para usar tabelas de processos
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoCadastroEconomico.class.php"   );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoDiasCadEcon.class.php"         );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoSociedade.class.php"           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoDomicilioFiscal.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoDomicilioInformado.class.php"  );
//INCLUDE DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS

class RCEMInscricaoEconomica
{
/**
    * @access Private
    * @var Integer
*/
var $codigo_processo;

/**
    * @access Private
    * @var String
*/
var $ano_exercicio;

/**
    * @access Private
    * @var Integer
*/
var $inscricaoEconomica;
var $inscricaoEconomicaInicial;
var $inscricaoEconomicaFinal;
/**
    * @access Private
    * @var String
*/
var $stdomicilioFiscal;
/**
    * @acess Private
    * @var String
*/
var $stdataAbertura;
/**
    * @access Private
    * @var Array
*/
var $arhorarioAtividade;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;
/**
    * @access Private
    * @var String
*/
var $stmotivoBaixa;
/**
    * @access Private
    * @var String
*/
var $stCodProcessoBaixa;
/**
    * @access Private
    * @var Integer
*/
var $inExercicioBaixa;
/**
    * @access Private
    * @var Boolean
*/
var $boDeOficio;

/**
    * @access Private
    * @var Object
*/
var $obTCEMProcessoDomicilioInformado;

/**
    * @access Private
    * @var Object
*/
var $obTCEMProcessoDomicilioFiscal;

/**
    * @access Private
    * @var Object
*/
var $obTCEMProcessoDiasCadEcon;

/**
    * @access Private
    * @var Object
*/
var $obTCEMProcessoCadastroEconomico;

/**
    * @access Private
    * @var Object
*/
var $obTCEMCadastroEconomico;
/**
    * @access Private
    * @var Array
*/
var $roUltimaInscricaoAtividade;
/**
    * @access Private
    * @var Array
*/
var $roUltimoImovel;
/**
    * @access Private
    * @var Array
*/
var $roUltimoProcesso;
/**
    * @access Private
    * @var Array
*/
var $roUltimoResponsavel;
/**
    * @access Private
    * @var Array
*/
var $roUltimoElemento;
/**
    * @access Private
    * @var Object
*/
var $obRCGMPessoaFisica;
/**
    * @access Private
    * @var Object
*/
var $obRCGMPessoaJuridica;
/**
    * @access Private
    * @var Object
*/
var $obRCEMAtividade;
/**
    * @access Private
    * @var Object
*/
var $obRCEMSociedade;
/**
    * @access Private
    * @var Object
*/
var $obRCEMDomicilio;
/**
    * @access Private
    * @var Object
*/
var $obRCEMNaturezaJuridica;
/**
    * @access Private
    * @var String
*/
//auxilia na filtragem ao montar lista (listarInscricao)
var $stTipoListagem;

/**
    * @access Private
    * @var Object
*/
var $obTCEMTipoBaixaInscricao;

/**
    * @access Private
    * @var integer
*/
var $inCodigoTipoDeBaixa;
var $inCodLicenca;
var $stLicencaExercicio;

function setCodLicenca($valor) { $this->inCodLicenca = $valor;}
function getCodLicenca() {return $this->inCodLicenca;}

function setLicencaExercicio($valor) { $this->stLicencaExercicio = $valor;}
function getLicencaExercicio() {return $this->stLicencaExercicio;}

//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipoDeBaixa($valor) { $this->inCodigoTipoDeBaixa = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setAnoExercicio($valor) { $this->ano_exercicio = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoProcesso($valor) { $this->codigo_processo = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoEconomica($valor) { $this->inscricaoEconomica = $valor; }
function setInscricaoEconomicaInicial($valor) { $this->inscricaoEconomicaInicial = $valor; }
function setInscricaoEconomicaFinal($valor) { $this->inscricaoEconomicaFinal = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDomicilioFiscal($valor) { $this->stdomicilioFiscal  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataAbertura($valor) { $this->stDtAbertura  = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setHorarioAtividade($valor) { $this->arhorarioAtividade = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa      = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataTermino($valor) { $this->dtDataTermino    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMotivoBaixa($valor) { $this->stmotivoBaixa    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodProcessoBaixa($valor) { $this->stCodProcessoBaixa  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setExercicioBaixa($valor) { $this->inExercicioBaixa  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDeOficio($valor) { $this->boDeOficio       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoListagem($valor) { $this->stTipoListagem       = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCodigoTipoDeBaixa() { return $this->inCodigoTipoDeBaixa; }

/**
    * @access Public
    * @return Integer $valor
*/
function getAnoExercicio() { return $this->ano_exercicio; }

/**
    * @access Public
    * @return Integer $valor
*/
function getCodigoProcesso() { return $this->codigo_processo; }

/**
    * @access Public
    * @return Integer
*/
function getInscricaoEconomica() { return $this->inscricaoEconomica; }
function getInscricaoEconomicaInicial() { return $this->inscricaoEconomicaInicial; }
function getInscricaoEconomicaFinal() { return $this->inscricaoEconomicaFinal; }
/**
    * @access Public
    * @return String
*/
function getDomicilioFiscal() { return $this->stdomicilioFiscal;  }
/**
    * @access Public
    * @return String
*/
function getDataAbertura() { return $this->stDtAbertura;  }
/**
    * @access Public
    * @return Array
*/
function getHorarioAtividade() { return $this->arhorarioAtividade; }
/**
    * @access Public
    * @return Date
*/
function getDataBaixa() { return $this->dtDataBaixa; }
/**
    * @access Public
    * @return Date
*/
function getDataTermino() { return $this->dtDataTermino; }
/**
    * @access Public
    * @return String
*/
function getMotivoBaixa() { return $this->stmotivoBaixa; }
/**
    * @access Public
    * @return String
*/
function getCodProcessoBaixa() { return $this->stCodProcessoBaixa; }
/**
    * @access Public
    * @return Integer
*/
function getExercicioBaixa() { return $this->inExercicioBaixa; }
/**
    * @access Public
    * @return Boolean
*/
function getDeOficio() { return $this->boDeOficio;    }
/**
    * @access Public
    * @return String
*/
function getTipoListagem() { return $this->stTipoListagem;    }

//METODO CONSTRUTOR
/**
    * Método construtor
    * @access Private
*/
function RCEMInscricaoEconomica()
{
    $this->obTCEMTipoBaixaInscricao = new TCEMTipoBaixaInscricao;
    $this->obTCEMProcessoDomicilioInformado = new TCEMProcessoDomicilioInformado;
    $this->obTCEMProcessoDomicilioFiscal  = new TCEMProcessoDomicilioFiscal;
    $this->obTCEMProcessoDiasCadEcon      = new TCEMProcessoDiasCadEcon;
    $this->obTCEMProcessoCadastroEconomico = new TCEMProcessoCadastroEconomico;
    $this->obTCEMCadastroEconomico        = new TCEMCadastroEconomico;
    $this->obTCEMCadastroAtributo         = new TCEMCadastroAtributo;
    $this->obTCEMDomicilioFiscal          = new TCEMDomicilioFiscal;
    $this->obTCEMDomicilioInformado       = new TCEMDomicilioInformado;
    $this->obTCEMCadastroEconRespContabil = new TCEMCadastroEconRespContabil;
    $this->obTCEMCadastroEconRespTecnico  = new TCEMCadastroEconRespTecnico;
    $this->obTCEMBaixaCadastroEconomico   = new TCEMBaixaCadastroEconomico;
    $this->obTCEMDiasCadastroEconomico    = new TCEMDiasCadastroEconomico;
    $this->obTCEMElementoAtivCadEconomico = new TCEMElementoAtivCadEconomico;
    $this->obTCEMCadastroEconRespTecnico  = new TCEMCadastroEconRespTecnico;
    $this->obTDiasSemana                  = new TDiasSemana;
    $this->obRCEMResponsavelTecnico       = new RCEMResponsavelTecnico;
    $this->obRCEMConfiguracao             = new RCEMConfiguracao;
    $this->obRCGM                         = new RCGM;
    $this->obRCGMPessoaJuridica           = new RCGMPessoaJuridica;
    $this->obRCGMPessoaFisica             = new RCGMPessoaFisica;
    $this->obRCEMAtividade                = new RCEMAtividade;
    $this->obRCEMSociedade 				  = new RCEMSociedade;
    $this->obRCEMDomicilio                = new RCEMDomicilio;
    $this->obRCEMNaturezaJuridica         = new RCEMNaturezaJuridica;
    $this->obTCEMLicencaAtividade         = new TCEMLicencaAtividade;
    $this->arRCEMInscricaoAtividade       = array();
    $this->arRCIMImovel                   = array();
    $this->arRProcesso                    = array();
    $this->arRCEMResponsavel              = array();
    $this->arRCEMElementos                = array();
    $this->obTransacao                    = new Transacao;
    $this->arHorario                      = array();
}

/**
    * Inclui os dados referentes a Inscricao Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;

    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $this->obRCEMConfiguracao->consultarConfiguracao( $boTransacao );
    $boNumeroInscricao = $this->obRCEMConfiguracao->getNumeroInscricao();
    if ( !$obErro->ocorreu() ) {
        if ($boNumeroInscricao == "t") {
            $obErro = $this->obTCEMCadastroEconomico->proximoCod( $inCodigoInscricao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->setInscricaoEconomica( $inCodigoInscricao );
            }
        }

        $this->obTCEMCadastroEconomico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
        $this->obTCEMCadastroEconomico->setDado( "dt_abertura", $this->getDataAbertura() );

        $obErro = $this->obTCEMCadastroEconomico->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRCEMDomicilio->getDomicilioExibir() == 'IC' ) {
                //inclusao na tabela domicilio_fiscal
                $this->obTCEMDomicilioFiscal->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
                $this->obTCEMDomicilioFiscal->setDado( "inscricao_municipal" , $this->obRCEMDomicilio->getDomicilioFiscal());
                $obErro = $this->obTCEMDomicilioFiscal->inclusao( $boTransacao );

                //incluindo na tabela processo_domicilio_fiscal
                if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso() ) {
                    $this->obTCEMProcessoDomicilioFiscal->setDado( "inscricao_economica", $this->getInscricaoEconomica() );

                    $this->obTCEMProcessoDomicilioFiscal->setDado( "timestamp", "('now'::text)::timestamp(3)");
                    $this->obTCEMProcessoDomicilioFiscal->setDado( "ano_exercicio", $this->getAnoExercicio());
                    $this->obTCEMProcessoDomicilioFiscal->setDado( "cod_processo",  $this->getCodigoProcesso());

                    $obErro = $this->obTCEMProcessoDomicilioFiscal->inclusao ( $boTransacao );
                }
            } else {
                //INCLUSAO na tabela DOMICILIO_INFORMADO
                $this->obTCEMDomicilioInformado->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                $this->obTCEMDomicilioInformado->setDado ( 'cod_logradouro', $this->obRCEMDomicilio->getCodLogradouro() );
                $this->obTCEMDomicilioInformado->setDado ( 'numero', $this->obRCEMDomicilio->getNumero() );
                $this->obTCEMDomicilioInformado->setDado ( 'cod_bairro', $this->obRCEMDomicilio->getCodBairro() );
                $this->obTCEMDomicilioInformado->setDado ( 'caixa_postal', $this->obRCEMDomicilio->getCaixaPostal() );
                $this->obTCEMDomicilioInformado->setDado ( 'cep', $this->obRCEMDomicilio->getCEP());
                $this->obTCEMDomicilioInformado->setDado ( 'complemento', $this->obRCEMDomicilio->getComplemento() );
                $this->obTCEMDomicilioInformado->setDado ( 'cod_municipio', $this->obRCEMDomicilio->getCodMunicipio());
                $this->obTCEMDomicilioInformado->setDado ( 'cod_uf', $this->obRCEMDomicilio->getCodUF() );
                $obErro = $this->obTCEMDomicilioInformado->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso() ) {
                    //incluindo na tabela processo_domicilio_informado
                    $this->obTCEMProcessoDomicilioInformado->setDado( "inscricao_economica", $this->getInscricaoEconomica() );

                    $this->obTCEMProcessoDomicilioInformado->setDado( "timestamp", "('now'::text)::timestamp(3)");
                    $this->obTCEMProcessoDomicilioInformado->setDado( "ano_exercicio", $this->getAnoExercicio());
                    $this->obTCEMProcessoDomicilioInformado->setDado( "cod_processo",  $this->getCodigoProcesso());

                    $obErro = $this->obTCEMProcessoDomicilioInformado->inclusao( $boTransacao );
                }
            }
            //----------------------------------------------------------------------- NOVA INCLUSAO FIM
            if (!$obErro->ocorreu() && ($this->getAnoExercicio() != "") && ($this->getCodigoProcesso() != "")) {
                //inserindo dados na tabela processo_cadastro_economico
                $this->obTCEMProcessoCadastroEconomico->setDado("inscricao_economica", $this->getInscricaoEconomica() );

                $this->obTCEMProcessoCadastroEconomico->setDado("ano_exercicio", $this->getAnoExercicio() );
                $this->obTCEMProcessoCadastroEconomico->setDado("cod_processo", $this->getCodigoProcesso() );

                $obErro = $this->obTCEMProcessoCadastroEconomico->inclusao( $boTransacao );
            }

            if ( !$obErro->ocorreu() && $this->obRCEMResponsavelTecnico->getNumCGM() ) { //agora nao eh mais obrigatorio para empresa de fato e autonomo
                $this->obTCEMCadastroEconRespContabil->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                $this->obTCEMCadastroEconRespContabil->setDado( "numcgm"              , $this->obRCEMResponsavelTecnico->getNumCGM() );

                $this->obTCEMCadastroEconRespContabil->setDado( "sequencia", $this->obRCEMResponsavelTecnico->getSequencia() );

                $obErro = $this->obTCEMCadastroEconRespContabil->inclusao( $boTransacao );
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

    return $obErro;
}

/**
    * Inclui os dados referentes a Inscricao Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function ConverterInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //----------------------------------------------------------------------- NOVA INCLUSAO DOMICILIOS
        if ( $this->obRCEMDomicilio->getDomicilioExibir() == 'IC' ) {
            $this->obTCEMDomicilioFiscal->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
            $this->obTCEMDomicilioFiscal->setDado( "inscricao_municipal" , $this->obRCEMDomicilio->getDomicilioFiscal());
            $obErro = $this->obTCEMDomicilioFiscal->inclusao( $boTransacao );
        } else {
            //INCLUSAO na tabela DOMICILIO_INFORMADO
            $this->obTCEMDomicilioInformado->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
            $this->obTCEMDomicilioInformado->setDado ( 'cod_logradouro', $this->obRCEMDomicilio->getCodLogradouro() );
            $this->obTCEMDomicilioInformado->setDado ( 'numero', $this->obRCEMDomicilio->getNumero() );
            $this->obTCEMDomicilioInformado->setDado ( 'cod_bairro',$this->obRCEMDomicilio->getCodBairro());
            $this->obTCEMDomicilioInformado->setDado ( 'caixa_postal', $this->obRCEMDomicilio->getCaixaPostal() );
            $this->obTCEMDomicilioInformado->setDado ( 'cep', $this->obRCEMDomicilio->getCEP());
            $this->obTCEMDomicilioInformado->setDado ( 'complemento', $this->obRCEMDomicilio->getComplemento() );
            $this->obTCEMDomicilioInformado->setDado ( 'cod_municipio', $this->obRCEMDomicilio->getCodMunicipio() );
            $this->obTCEMDomicilioInformado->setDado ( 'cod_uf', $this->obRCEMDomicilio->getCodUF() );
            $obErro = $this->obTCEMDomicilioInformado->inclusao( $boTransacao );
        }
        //----------------------------------------------------------------------- NOVA INCLUSAO FIM
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCadastroEconRespContabil->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
            $this->obTCEMCadastroEconRespContabil->setDado( "numcgm"              , $this->obRCEMResponsavelTecnico->getNumCGM() );
            $this->obTCEMCadastroEconRespContabil->setDado( "sequencia", $this->obRCEMResponsavelTecnico->getSequencia() );
            $obErro = $this->obTCEMCadastroEconRespContabil->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

    return $obErro;
}

/**
    * Altera os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTCEMCadastroEconomico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
        $this->obTCEMCadastroEconomico->setDado( "dt_abertura",         $this->getDataAbertura() );
        $obErro = $this->obTCEMCadastroEconomico->alteracao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            //MESMO NA ALTERAÇÃO, A AÇÃO SERÉ DE INCLUSÃO, Utilizando timestamp para fazer historico
            if ( $this->obRCEMDomicilio->getDomicilioExibir() == 'IC' ) {
                $this->obTCEMDomicilioFiscal->setDado( "inscricao_economica" , $this->getInscricaoEconomica());
                $this->obTCEMDomicilioFiscal->setDado( "inscricao_municipal" , $this->obRCEMDomicilio->getDomicilioFiscal());
                $obErro = $this->obTCEMDomicilioFiscal->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso() ) {
                    //incluindo na tabela processo_domicilio_fiscal
                    $this->obTCEMProcessoDomicilioFiscal->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
                    $this->obTCEMProcessoDomicilioFiscal->setDado( "ano_exercicio", $this->getAnoExercicio());
                    $this->obTCEMProcessoDomicilioFiscal->setDado( "cod_processo",  $this->getCodigoProcesso());
                    $this->obTCEMProcessoDomicilioFiscal->setDado("timestamp","('now'::text)::timestamp(3)");

                    $obErro = $this->obTCEMProcessoDomicilioFiscal->inclusao ( $boTransacao );
                }
            } else {
                //INCLUSAO na tabela DOMICILIO_INFORMADO
                $this->obTCEMDomicilioInformado->setDado ("inscricao_economica",$this->getInscricaoEconomica());
                $this->obTCEMDomicilioInformado->setDado ( 'cod_logradouro', $this->obRCEMDomicilio->getCodLogradouro() );
                $this->obTCEMDomicilioInformado->setDado ( 'numero', $this->obRCEMDomicilio->getNumero() );
                $this->obTCEMDomicilioInformado->setDado ( 'complemento', $this->obRCEMDomicilio->getComplemento());
                $this->obTCEMDomicilioInformado->setDado ('cod_bairro', $this->obRCEMDomicilio->getCodBairro());
                $this->obTCEMDomicilioInformado->setDado ( 'caixa_postal', $this->obRCEMDomicilio->getCaixaPostal() );
                $this->obTCEMDomicilioInformado->setDado ( 'cep', $this->obRCEMDomicilio->getCEP());
                $this->obTCEMDomicilioInformado->setDado ('cod_municipio', $this->obRCEMDomicilio->getCodMunicipio());
                $this->obTCEMDomicilioInformado->setDado ('cod_uf', $this->obRCEMDomicilio->getCodUF());

                $obErro = $this->obTCEMDomicilioInformado->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso() ) {
                    //incluindo na tabela processo_domicilio_informado
                    $this->obTCEMProcessoDomicilioInformado->setDado( "inscricao_economica", $this->getInscricaoEconomica() );

                    $this->obTCEMProcessoDomicilioInformado->setDado( "ano_exercicio", $this->getAnoExercicio());
                    $this->obTCEMProcessoDomicilioInformado->setDado( "cod_processo",  $this->getCodigoProcesso());
                    $this->obTCEMProcessoDomicilioInformado->setDado("timestamp","('now'::text)::timestamp(3)");

                    $obErro = $this->obTCEMProcessoDomicilioInformado->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() && $this->obRCEMResponsavelTecnico->getNumCGM() ) { //nao eh mais obrigatorio para empresa de fato e autonomo
                $stFiltro = " WHERE inscricao_economica = ".$this->getInscricaoEconomica();
                $this->obTCEMCadastroEconRespContabil->recuperaTodos( $rsLista, $stFiltro, " timestamp DESC LIMIT 1", $boTransacao );
                $boIncluir = true;
                if ( !$rsLista->Eof() ) {
                    if ( $rsLista->getCampo("numcgm") == $this->obRCEMResponsavelTecnico->getNumCGM() ) {
                        $boIncluir = false;
                    }
                }

                $this->obTCEMCadastroEconRespContabil->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                $this->obTCEMCadastroEconRespContabil->setDado( "numcgm"              , $this->obRCEMResponsavelTecnico->getNumCGM() );
                $this->obTCEMCadastroEconRespContabil->setDado( "sequencia", $this->obRCEMResponsavelTecnico->getSequencia() );

                if ( $boIncluir )
                    $obErro = $this->obTCEMCadastroEconRespContabil->inclusao( $boTransacao );
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

    return $obErro;
}

/**
    * Altera os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarInscricaoDomicilio($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->obRCEMDomicilio->getDomicilioExibir() == 'IC' ) {
            $this->obTCEMDomicilioFiscal->setDado( "inscricao_economica" , $this->getInscricaoEconomica());

            $this->obTCEMDomicilioFiscal->setDado( "inscricao_municipal" , $this->obRCEMDomicilio->getDomicilioFiscal());
            $obErro = $this->obTCEMDomicilioFiscal->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso()) {
                //incluindo na tabela processo_domicilio_fiscal
                $this->obTCEMProcessoDomicilioFiscal->setDado( "inscricao_economica", $this->getInscricaoEconomica() );

                $this->obTCEMProcessoDomicilioFiscal->setDado( "ano_exercicio", $this->getAnoExercicio());
                $this->obTCEMProcessoDomicilioFiscal->setDado( "cod_processo",  $this->getCodigoProcesso());

                $obErro = $this->obTCEMProcessoDomicilioFiscal->inclusao ( $boTransacao );
            }
        } else {
            //INCLUSAO na tabela DOMICILIO_INFORMADO
            $this->obTCEMDomicilioInformado->setDado ("inscricao_economica",$this->getInscricaoEconomica());
            $this->obTCEMDomicilioInformado->setDado ( 'cod_logradouro', $this->obRCEMDomicilio->getCodLogradouro() );
            $this->obTCEMDomicilioInformado->setDado ( 'numero', $this->obRCEMDomicilio->getNumero() );
            $this->obTCEMDomicilioInformado->setDado ( 'complemento', $this->obRCEMDomicilio->getComplemento());
            $this->obTCEMDomicilioInformado->setDado ('cod_bairro', $this->obRCEMDomicilio->getCodBairro());
            $this->obTCEMDomicilioInformado->setDado ( 'caixa_postal', $this->obRCEMDomicilio->getCaixaPostal() );
            $this->obTCEMDomicilioInformado->setDado ( 'cep', $this->obRCEMDomicilio->getCEP());
            $this->obTCEMDomicilioInformado->setDado ('cod_municipio', $this->obRCEMDomicilio->getCodMunicipio());
            $this->obTCEMDomicilioInformado->setDado ('cod_uf', $this->obRCEMDomicilio->getCodUF());

            $obErro = $this->obTCEMDomicilioInformado->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso() ) {
                //incluindo na tabela processo_domicilio_informado
                $this->obTCEMProcessoDomicilioInformado->setDado( "inscricao_economica", $this->getInscricaoEconomica() );

                $this->obTCEMProcessoDomicilioInformado->setDado( "ano_exercicio", $this->getAnoExercicio());
                $this->obTCEMProcessoDomicilioInformado->setDado( "cod_processo",  $this->getCodigoProcesso());

                $obErro = $this->obTCEMProcessoDomicilioInformado->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMDomicilioFiscal );

    return $obErro;
}

/**
    * Excluir os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirInscricao($boTransacao = "")
{
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->roUltimaInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
    $this->roUltimaInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado( "timestamp", "" );
    $obErro = $this->roUltimaInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }
    
    $stFiltro = " WHERE inscricao_economica = ". $this->getInscricaoEconomica();
    $this->obTCEMLicencaAtividade->recuperaTodos ( $rsLicenca, $stFiltro, "", $boTransacao );

    if ( $rsLicenca->getNumLinhas() > 0 ) {
        $obErro->setDescricao("Inscrição Econômica: ".$this->getInscricaoEconomica()." - Esta Inscrição Econômica não pode ser excluída porque está sendo utilizado pelo sistema.");
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }
    
    $obErro = $this->roUltimaInscricaoAtividade->excluirAtividadeInscricao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $this->obTCEMProcessoDiasCadEcon->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
    $this->obTCEMProcessoDiasCadEcon->setDado( "timestamp", "" );
    $obErro = $this->obTCEMProcessoDiasCadEcon->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $this->obTCEMDiasCadastroEconomico->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMDiasCadastroEconomico->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $this->obTCEMCadastroEconRespContabil->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMCadastroEconRespContabil->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->obTCEMProcessoDomicilioInformado->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $this->obTCEMProcessoDomicilioInformado->setDado( "timestamp" , "" );
    $obErro = $this->obTCEMProcessoDomicilioInformado->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->obTCEMDomicilioInformado->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMDomicilioInformado->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->obTCEMProcessoDomicilioFiscal->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $this->obTCEMProcessoDomicilioFiscal->setDado( "timestamp" , "" );
    $obErro = $this->obTCEMProcessoDomicilioFiscal->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->obTCEMDomicilioFiscal->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMDomicilioFiscal->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->obTCEMProcessoCadastroEconomico->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMProcessoCadastroEconomico->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

        return $obErro;
    }

    $this->obTCEMCadastroEconomico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMCadastroEconomico->exclusao( $boTransacao );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

    return $obErro;
}

function definirAtividadeAlterar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roUltimaInscricaoAtividade->consultarAtividadesInscricao( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arAtividade = array();
            while ( !$rsRecordSet->eof() ) {
                $inChaveAtividade = $rsRecordSet->getCampo( "cod_atividade" );
                $arAtividade[$inChaveAtividade] = true;
                $rsRecordSet->proximo();
            }
            $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade($this);
            $obErro = $obRCEMInscricaoAtividade->gerarOcorrenciaAtividade($inOcorrencia, $boTransacao );
            if (!$obErro->ocorreu() ) {
                foreach ($this->arRCEMInscricaoAtividade as $obRCEMInscricaoAtividade) {
                    $inChave = $this->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade();
                    if ( !isset($arAtividade[$inChaveAtividade]) ) {
                        $this->roUltimaInscricaoAtividade->setOcorrencia($inOcorrencia);
                        $obErro = $this->roUltimaInscricaoAtividade->incluirAtividadeInscricao( $boTransacao );
                    } else {
                        $arAtividade[$inChave] = "";
                    }
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    foreach ($arAtividade as $inKey => $valor) {
                        $this->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $inKey );
                        $obErro = $this->roUltimaInscricaoAtividade->excluirAtividadeInscricao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomico );

    return $obErro;
}

/**
    * Define atividades para inscrição economica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function definirAtividade($boTransacao = "")
{
    $boFlagTransacao = false;
    $boPrimeiraVez = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $tmpCodigoAtividade = $this->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade();
        $this->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade("");

        $obErro = $this->roUltimaInscricaoAtividade->consultarAtividadesInscricao( $rsRecordSet, $boTransacao );

        $this->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $tmpCodigoAtividade );
        if ( !$obErro->ocorreu() ) {
            $arAtividade = array();
            $arOcorrencia = array();
            while ( !$rsRecordSet->eof() ) {
                $inChaveAtividade = $rsRecordSet->getCampo( "cod_atividade" );
                $arAtividade[$inChaveAtividade] = true;
                $arOcorrencia[$inChaveAtividade] = $rsRecordSet->getCampo( "ocorrencia_atividade" );
                $rsRecordSet->proximo();
            }

            foreach ($this->arRCEMInscricaoAtividade as $obRCEMInscricaoAtividade) {
                    if ($boPrimeiraVez) {
                        $stTmpCampoCod = $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->getCampoCod();
                        $stTmpComplementoChave = $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->getComplementoChave();
                        $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setCampoCod('ocorrencia_atividade');
                        $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setComplementoChave('inscricao_economica');
                        $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado('inscricao_economica',$this->getInscricaoEconomica());
                        $obErro = $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->proximoCod( $inOcorrencia , $boTransacao );
                        $boPrimeiraVez = false;
                        $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setCampoCod($stTmpCampoCod);
                        $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setComplementoChave($stTmpComplementoChave);
                    }
                    $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado( "principal" , $obRCEMInscricaoAtividade->getPrincipal() );
                    $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
                    $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado( "cod_atividade" , $obRCEMInscricaoAtividade->roUltimaAtividade->getCodigoAtividade() );
                    $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado( "dt_inicio" , $obRCEMInscricaoAtividade->getDataInicio() );
                    $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado( "dt_termino" , $obRCEMInscricaoAtividade->getDataTermino());
                    if ( !$obErro->ocorreu() ) {
                        $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->setDado( "ocorrencia_atividade" , $inOcorrencia );
                        $obErro = $obRCEMInscricaoAtividade->obTCEMAtividadeCadastroEconomico->inclusao( $boTransacao );
                    }

                    if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso() ) {
                        //inserindo na tabela processo_atividade_cad_econ
                        $obRCEMInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado("cod_atividade", $obRCEMInscricaoAtividade->roUltimaAtividade->getCodigoAtividade());

                        $obRCEMInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado("inscricao_economica", $this->getInscricaoEconomica());

                        $obRCEMInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado("ocorrencia_atividade", $inOcorrencia);

                        $obRCEMInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado("cod_processo", $this->getCodigoProcesso());

                        $obRCEMInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->setDado("ano_exercicio", $this->getAnoExercicio());

                        $obErro = $obRCEMInscricaoAtividade->obTCEMProcessoAtividadeCadEcon->inclusao( $boTransacao );
                    }

                    if ($obErro->ocorreu())
                        break;
            }

            if ( !$obErro->ocorreu() && $this->getHorarioAtividade() ) {
                foreach ( $this->getHorarioAtividade() as $key => $arValor ) {
                    $this->obTCEMDiasCadastroEconomico->setDado( "cod_dia"             , $arValor["inDia"] );
                    $this->obTCEMDiasCadastroEconomico->setDado( "hr_inicio"           , $arValor["hrInicio"] );
                    $this->obTCEMDiasCadastroEconomico->setDado( "hr_termino"          , $arValor["hrTermino"] );
                    $this->obTCEMDiasCadastroEconomico->setDado( "inscricao_economica" , $this->roUltimaInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                    $obErro = $this->obTCEMDiasCadastroEconomico->inclusao( $boTransacao );
                    if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso()) {
                        //inserindo dados na tabela processo_dias_cad_econ
                        $this->obTCEMProcessoDiasCadEcon->setDado("inscricao_economica", $this->roUltimaInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );

                        $this->obTCEMProcessoDiasCadEcon->setDado("cod_dia", $arValor["inDia"] );
                        $this->obTCEMProcessoDiasCadEcon->setDado("ano_exercicio", $this->getAnoExercicio() );
                        $this->obTCEMProcessoDiasCadEcon->setDado("cod_processo", $this->getCodigoProcesso() );

                        $obErro = $this->obTCEMProcessoDiasCadEcon->inclusao( $boTransacao );
                    }

                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMDiasCadastroEconomico );

    return $obErro;
}

/**
   * Define responsáveis para inscrição economica
   * @access Public
   * @param  Object $obTransacao Parâmetro Transação
   * @return Object Objeto Erro
*/
function definirResponsavel($boTransacao = "")
{
   $boFlagTransacao = false;
   $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->obTCEMCadastroEconRespTecnico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
        $this->obTCEMCadastroEconRespTecnico->setDado( "ativo", false );
        $obErro = $this->obTCEMCadastroEconRespTecnico->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arResponsaveisSessao = Sessao::read( "responsaveis" );
            if ( count($arResponsaveisSessao > 0 ) ) {
                foreach ($arResponsaveisSessao as $key) {
                    $this->obTCEMCadastroEconRespTecnico->setDado( "inscricao_economica", $_REQUEST['inInscricaoEconomica'] );
                    $this->obTCEMCadastroEconRespTecnico->setDado( "numcgm", $key['inNumCGM'] );
                    $this->obTCEMCadastroEconRespTecnico->setDado( "sequencia", $key['sequencia'] );
                    $this->obTCEMCadastroEconRespTecnico->setDado( "ativo", true );
                    $obErro = $this->obTCEMCadastroEconRespTecnico->inclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }

   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconRespTecnico );

   return $obErro;
}

/**
    * Define elementos para inscrição economica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function definirElementos($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // desativar elementos anteriores!
        $obErro = $this->listarElementosAtivos($rsElementosAtivos,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            while ( !$rsElementosAtivos->eof() ) {
                $this->obTCEMElementoAtivCadEconomico->setDado( "inscricao_economica"  , $rsElementosAtivos->getCampo("inscricao_economica"));
                $this->obTCEMElementoAtivCadEconomico->setDado( "cod_atividade"        , $rsElementosAtivos->getCampo("cod_atividade"));
                $this->obTCEMElementoAtivCadEconomico->setDado( "ocorrencia_atividade" , $rsElementosAtivos->getCampo("ocorrencia_atividade"));
                $this->obTCEMElementoAtivCadEconomico->setDado( "cod_elemento"         , $rsElementosAtivos->getCampo("cod_elemento"));
                $this->obTCEMElementoAtivCadEconomico->setDado( "ocorrencia_elemento"  , $rsElementosAtivos->getCampo("ocorrencia_elemento"));
                $this->obTCEMElementoAtivCadEconomico->setDado( "ativo"                , false );
                $obErro = $this->obTCEMElementoAtivCadEconomico->alteracao( $boTransacao );
                if ( $obErro->ocorreu() ) break;
                $rsElementosAtivos->proximo();
            }
        }
        if ( !$obErro->ocorreu() ) {
            foreach ($this->arRCEMElemento as $obRCEMElemento) {
                $this->obTCEMElementoAtivCadEconomico = new TCEMElementoAtivCadEconomico;
                $this->obTCEMElementoAtivCadEconomico->setDado( "inscricao_economica"  , $this->getInscricaoEconomica() );
                $this->obTCEMElementoAtivCadEconomico->setDado( "cod_atividade"        , $obRCEMElemento->roCEMAtividade->getCodigoAtividade() );
                $this->obTCEMElementoAtivCadEconomico->setDado( "ocorrencia_atividade" , $obRCEMElemento->roCEMAtividade->getOcorrenciaAtividade() );
                $this->obTCEMElementoAtivCadEconomico->setDado( "cod_elemento"         , $obRCEMElemento->getCodigoElemento() );
                $obRCEMElemento->setOcorrenciaElemento( $obRCEMElemento->getOcorrenciaElemento() +1);
                $this->obTCEMElementoAtivCadEconomico->setDado( "ocorrencia_elemento"  , $obRCEMElemento->getOcorrenciaElemento() );
                $this->obTCEMElementoAtivCadEconomico->setDado( "ativo"                , true );
                $obErro = $this->obTCEMElementoAtivCadEconomico->inclusao( $boTransacao );

                if ( $obErro->ocorreu() ) {
                    break;
                }
                if ( !$obErro->ocorreu() ) {
                    $arTmp = $obRCEMElemento->getArrayElemento();
                    foreach ($arTmp as $valor => $key) {
                        foreach ($key as $val => $chave) {
                            $obRCEMElemento->obRCadastroDinamico->addAtributosDinamicos( $val, $chave );
                            $arChaveAtributo =  array( "cod_elemento"        => $obRCEMElemento->getCodigoElemento(),
                                                       "inscricao_economica" => $this->getInscricaoEconomica(),
                                                       "cod_atividade"       => $obRCEMElemento->roCEMAtividade->getCodigoAtividade(),
                                                       "ocorrencia_atividade"=> $obRCEMElemento->roCEMAtividade->getOcorrenciaAtividade(),
                                                       "ocorrencia_elemento" => $obRCEMElemento->getOcorrenciaElemento() );

                            $obRCEMElemento->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                            $obErro = $obRCEMElemento->obRCadastroDinamico->salvarValores( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                        $obRCEMElemento->obRCadastroDinamico->setAtributosDinamicos( array() );

                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoAtivCadEconomico );

    return $obErro;
}

/**
* Salva Atributos dinamicos de elementos
*/
function salvarElementosAtividadeEconomico($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao = "" );
    if ( !$obErro->ocorreu() ) {
        $arTmp = $roUltimoElemento->getArrayElemento();
        foreach ($arTmp as $valor => $key) {
            foreach ($key as $val => $chave) {
                $this->roUltimoElemento->obRCadastroDinamico->addAtributosDinamicos( $val, $chave );
                $arChaveAtributo =  array( "cod_elemento"        => $this->robUltimoElemento->getCodigoElemento(),
                                           "inscricao_economica" => $this->getInscricaoEconomica(),
                                           "cod_atividade"       => $this->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade(),
                                           "ocorrencia_atividade"=> $this->roUltimaInscricaoAtividade->roUltimaAtividade->getOcorrenciaAtividade(),
                                           "ocorrencia_elemento" => $this->roUltimoElemento->getOcorrenciaElemento() );

                $this->roUltimoElemento->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->roUltimoElemento->obRCadastroDinamico->salvarValores( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
            $this->robUltimoElemento->obRCadastroDinamico->setAtributosDinamicos( array() );

            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoAtivCadEconomico );

    return $obErro;
}

/**
    * Alterar horários para inscrição economica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarHorarios($boTransacao = "")
{
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        foreach ( $this->getHorarioAtividade() as $key => $arValor ) {
            $this->obTCEMDiasCadastroEconomico->setDado("cod_dia"            , $arValor["inDia"]);
            $this->obTCEMDiasCadastroEconomico->setDado("inscricao_economica", $this->getInscricaoEconomica());
            $obErro = $this->obTCEMDiasCadastroEconomico->exclusao( $boTransacao );
        }
        
        foreach ( $this->getHorarioAtividade() as $key => $arValor ) {
            $timestamp = sistemaLegado::dataToSql(date( "d/m/Y")).date(" H:i:s");
            $this->obTCEMDiasCadastroEconomico->setDado( "cod_dia"            , $arValor["inDia"] );
            $this->obTCEMDiasCadastroEconomico->setDado( "hr_inicio"          , $arValor["hrInicio"] );
            $this->obTCEMDiasCadastroEconomico->setDado( "hr_termino"         , $arValor["hrTermino"] );
            $this->obTCEMDiasCadastroEconomico->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $this->obTCEMDiasCadastroEconomico->setDado( "timestamp"          , $timestamp );

            $obErro = $this->obTCEMDiasCadastroEconomico->inclusao( $boTransacao );
            
            if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso()) {
                //inserindo dados na tabela processo_dias_cad_econ
                $this->obTCEMProcessoDiasCadEcon->setDado("inscricao_economica", $this->roUltimaInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                $this->obTCEMProcessoDiasCadEcon->setDado("cod_dia", $arValor["inDia"] );
                $this->obTCEMProcessoDiasCadEcon->setDado("ano_exercicio", $this->getAnoExercicio() );
                $this->obTCEMProcessoDiasCadEcon->setDado("cod_processo", $this->getCodigoProcesso() );
                $this->obTCEMProcessoDiasCadEcon->setDado("timestamp", $timestamp );
                $obErro = $this->obTCEMProcessoDiasCadEcon->inclusao( $boTransacao );
            }/*else{
                $obErro->setDescricao("Não é possível duplicar os dias da semana já cadastrados na lista de horários.");
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                break;
             }*/

            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMDiasCadastroEconomico );

    return $obErro;
}

/**
    * Reativar inscrição econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function reativarCadastroEconomico($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " where inscricao_economica = ".$this->getInscricaoEconomica();
        $stOrdem = " ORDER BY timestamp DESC LIMIT 1";

        $this->obTCEMBaixaCadastroEconomico->recuperaTodos( $rsBaixa, $stFiltro, $stOrdem, $boTransacao );
        $this->obTCEMBaixaCadastroEconomico->setDado( "dt_inicio", $rsBaixa->getCampo("dt_inicio") );
        $this->obTCEMBaixaCadastroEconomico->setDado( "inscricao_economica", $rsBaixa->getCampo("inscricao_economica") );
        $this->obTCEMBaixaCadastroEconomico->setDado( "motivo", $rsBaixa->getCampo("motivo") );
        $this->obTCEMBaixaCadastroEconomico->setDado( "de_oficio", $rsBaixa->getCampo("de_oficio") );
        $this->obTCEMBaixaCadastroEconomico->setDado( "cod_tipo", $rsBaixa->getCampo("cod_tipo") );
        $this->obTCEMBaixaCadastroEconomico->setDado( "timestamp", $rsBaixa->getCampo("timestamp") );
        $this->obTCEMBaixaCadastroEconomico->setDado( "dt_termino", $this->getDataTermino() );
        $obErro = $this->obTCEMBaixaCadastroEconomico->alteracao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMDiasCadastroEconomico );

    return $obErro;
}

/**
    * Baixar os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function baixarInscricao($boTransacao = "")
{
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaCadastroEconomico->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
        $this->obTCEMBaixaCadastroEconomico->setDado( "dt_inicio"           , $this->getDataBaixa() );
        $this->obTCEMBaixaCadastroEconomico->setDado( "motivo"              , $this->getMotivoBaixa() );
        $this->obTCEMBaixaCadastroEconomico->setDado( "de_oficio"           , $this->getDeOficio() );
        $this->obTCEMBaixaCadastroEconomico->setDado( "cod_tipo"            , $this->getCodigoTipoDeBaixa() );

        $obErro = $this->obTCEMBaixaCadastroEconomico->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->getCodProcessoBaixa() ) {
                include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoBaixaCadEconomico.class.php"      );
                $obTCEMBaixaProcesso = new TCEMProcessoBaixaCadEconomico;

                $obTCEMBaixaProcesso->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                $obTCEMBaixaProcesso->setDado( "dt_inicio"           , $this->getDataBaixa() );
                $obTCEMBaixaProcesso->setDado( "exercicio"           , $this->getExercicioBaixa() );
                $obTCEMBaixaProcesso->setDado( "cod_processo"        , $this->getCodProcessoBaixa() );

                $obErro = $obTCEMBaixaProcesso->inclusao( $boTransacao );
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaCadastroEconomico );

    return $obErro;
}

/**
    * Lista os dados referentes cadastro atributos
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCadastroAtributo(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    $stOrdem  = " ORDER BY cod_cadastro LIMIT 3";

    $obErro = $this->obTCEMCadastroAtributo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os dados referentes inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarInscricaoBaixa(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND cadastro_economico.inscricao_economica = ".$this->getInscricaoEconomica()." \n";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm = ".$this->obRCGM->getNumCGM() ."                                \n";
    }
    if ( $this->obRCGM->getNomCGM() ) {
        $stFiltro .= " AND cgm.nom_cgm like '%".$this->obRCGM->getNomCGM() ."%'                        \n";
    }
    if ( $this->obRCGMPessoaJuridica->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm = ".$this->obRCGMPessoaJuridica->getNumCGM() ."                  \n";
    }
    $stOrdem  =" ORDER BY cadastro_economico.inscricao_economica                                       \n";

    $obErro = $this->obTCEMCadastroEconomico->recuperaInscricaoBaixa( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os dados referentes inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND ce.inscricao_economica = ".$this->getInscricaoEconomica();
    }

    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm = ".$this->obRCGM->getNumCGM() ." ";
    }

    if ( $this->obRCGM->getNomCGM() ) {
        $stFiltro .= " AND lower(cgm.nom_cgm) like lower('".$this->obRCGM->getNomCGM()."') ";
    }

    if ( $this->obRCGMPessoaJuridica->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm = ".$this->obRCGMPessoaJuridica->getNumCGM() ." ";
    }

    if ( $this->getTipoListagem () != 'domicilio' ) {
        $stFiltro .= "      AND CASE                                                                           \n";
        $stFiltro .= "               WHEN ba.inscricao_economica IS NOT NULL THEN                              \n";
        $stFiltro .= "               CASE                                                                      \n";
        $stFiltro .= "                     WHEN ba.dt_termino IS NOT NULL THEN                                 \n";
        $stFiltro .= "                         true                                                            \n";
        $stFiltro .= "                     ELSE                                                                \n";
        $stFiltro .= "                         false                                                           \n";
        $stFiltro .= "               END                                                                       \n";
        $stFiltro .= "               ELSE                                                                      \n";
        $stFiltro .= "                     true                                                                \n";
        $stFiltro .= "               END                                                                       \n";
    }

    $stOrdem  = " order by ce.inscricao_economica ";

    $obErro = $this->obTCEMCadastroEconomico->recuperaInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os dados referentes inscrição econômica (lista da consulta)
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarInscricaoConsulta(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND CE.inscricao_economica = ".$this->getInscricaoEconomica();
    } elseif ( $this->getInscricaoEconomicaInicial() || $this->getInscricaoEconomicaFinal() ) {
        if ( $this->getInscricaoEconomicaInicial() && $this->getInscricaoEconomicaFinal() ) {
            $stFiltro .= " AND CE.inscricao_economica between ".$this->getInscricaoEconomicaInicial();
            $stFiltro .= " AND ".$this->getInscricaoEconomicaFinal();
        } elseif ($this->getInscricaoEconomicaInicial() && !$this->getInscricaoEconomicaFinal()) {
            $stFiltro .= " AND CE.inscricao_economica = ".$this->getInscricaoEconomicaInicial();
        } else {
            $stFiltro .= " AND CE.inscricao_economica = ".$this->getInscricaoEconomicaFinal();
        }

    }

    if ( $this->obRCGMPessoaJuridica->getCNPJ() ) {
        $stFiltro .= " AND CGMPJ.cnpj like '".trim($this->obRCGMPessoaJuridica->getCNPJ())."%'";
    }
    if ( $this->obRCGMPessoaFisica->getCPF() ) {
        $stFiltro .= " AND CGMPF.cpf like '".trim($this->obRCGMPessoaFisica->getCPF())."%'";
    }
    if ( $this->obRCGM->getNomCGM() ) {
        $stFiltro .= " AND UPPER(CGM.nom_cgm) like UPPER('".trim($this->obRCGM->getNomCGM())."%' ) ";
    }
    if ( $this->obRCEMAtividade->getValorComposto() ) {
        $stFiltro .= " AND A.cod_estrutural = '".trim($this->obRCEMAtividade->getValorComposto())."'";
    }
    if ( $this->obRCEMSociedade->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND soc.numcgm = ".$this->obRCEMSociedade->obRCGM->getNumCGM();
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm = ".$this->obRCGM->getNumCGM();
    }

    if ( $this->getDomicilioFiscal() ) {
        $stFiltro .= " AND DF.inscricao_municipal = ".$this->getDomicilioFiscal();
    }
    if ( $this->obRCEMNaturezaJuridica->getCodigoNatureza() ) {
        $stFiltro .= " AND EDNJ.cod_natureza = ".$this->obRCEMNaturezaJuridica->getCodigoNatureza();
    }
    if ( $this->getCodLicenca() ) {
         $stFiltro .= " AND el.cod_licenca = ".$this->getCodLicenca();
    }
    if ( $this->getLicencaExercicio() ) {
         $stFiltro .= " AND el.exercicio = ".$this->getLicencaExercicio();
    }
    $obErro = $this->obTCEMCadastroEconomico->recuperaListaConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os dados referentes inscrição econômica (lista da consulta)
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarInscricaoEconomica(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND CE.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    $obErro = $this->obTCEMCadastroEconomico->recuperaConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os dados referentes inscrição econômica BAIXADA (lista da consulta)
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarInscricaoEconomicaBaixa(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND cadastro_economico.inscricao_economica = ".$this->getInscricaoEconomica();
    }

    $stOrdem = " ORDER BY timestamp DESC LIMIT 1 ";
    $obErro = $this->obTCEMCadastroEconomico->recuperaInscricaoBaixa( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os responsáveis técnicos e/ou empresas cadastradas para uma inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarResponsaveisCadastro(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " and cadastro_econ_resp_tecnico.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    $stOrdem  = " cadastro_econ_resp_tecnico.numcgm ";

    $obErro = $this->obTCEMCadastroEconRespTecnico->recuperaRelacionamentoCadastro( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os responsáveis técnicos cadastrados para uma inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarResponsaveisInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " and cert.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    $stOrdem  = " cert.numcgm ";

    $obErro = $this->obTCEMCadastroEconRespTecnico->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Verifica se o domicílio informado já está ocupado por uma empresa
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificarDomicilio(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " and ce.inscricao_economica = ".$this->getInscricaoEconomica();
    }

    if ( $this->getDomicilioFiscal() ) {
        $stFiltro .= " and ce.cod_natureza = ".$this->getDomicilioFiscal();
    }

    $stOrdem = " order by ce.inscricao_economica ";

    $obErro = $this->obTCEMDomicilioFiscal->recuperaDomicilioFiscal( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Adiciona um objeto de inscricaoatividade
    * @access Public
*/
function addInscricaoAtividade()
{
    $this->arRCEMInscricaoAtividade[] = new RCEMInscricaoAtividade( $this );
    $this->roUltimaInscricaoAtividade = &$this->arRCEMInscricaoAtividade[ count($this->arRCEMInscricaoAtividade) - 1 ];
}

/**
    * Adiciona um objeto de imóvel
    * @access Public
*/
function addImovel()
{
    $this->arRCEMImovel[] = new RCEMImovel( $this );
    $this->roUltimoImovel = &$this->arRCIMImovel[ count( $this->arRCIMImovel ) - 1 ];
}

/**
    * Adiciona um objeto processo
    * @access Public
*/
function addProcesso()
{
    $this->arRProcesso[] = new RProcesso( $this );
    $this->roUltimoProcesso = &$this->arRProcesso[ count( $this->arRProcesso ) - 1 ];
}

/**
    * Adiciona um objeto responsaveltecnico
    * @access Public
*/
function addResponsavel()
{
    $this->arRCEMResponsavel[] = new RCEMResponsavelTecnico( $this );
    $this->roUltimoResponsavel = &$this->arRCEMResponsavel[ count( $this->arRCEMResponsavel ) - 1 ];
}

/**
    * Adiciona um objeto elemento
    * @access Public
*/
function addElemento()
{
    $this->arRCEMElemento[] = new RCEMElemento( $this );
    $this->roUltimoElemento = &$this->arRCEMElemento[ count( $this->arRCEMElemento ) - 1];
}
/**
    * Adiciona um objeto elemento
    * @access Public
*/
function addElementoAtividade()
{
    $this->arRCEMElemento[] = new RCEMElemento( new RCEMAtividade );
    $this->roUltimoElemento = &$this->arRCEMElemento[ count( $this->arRCEMElemento ) - 1];
}

/**
    * Lista os dias da semana
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDiasSemana(&$rsLista, $boTransacao = "")
{
    $stOrder = " ORDER BY cod_dia ";
    $obErro = $this->obTDiasSemana->recuperaTodos( $rsLista, "", $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista os horários da inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarInscricaoHorarios(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " and d.inscricao_economica = ". $this->getInscricaoEconomica();
    }

    $stOrdem = " ORDER BY d.cod_dia ";
    $obErro = $this->obTCEMDiasCadastroEconomico->recuperaEmpresaHorarios( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function alterarElementos($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arRCEMElemento as $obRCEMElemento) {
            $this->obTCEMElementoAtivCadEconomico->setDado( "inscricao_economica"  , $this->getInscricaoEconomica() );
            $this->obTCEMElementoAtivCadEconomico->setDado( "cod_atividade"        , $this->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade() );
            $this->obTCEMElementoAtivCadEconomico->setDado( "ocorrencia_atividade" , $this->roUltimaInscricaoAtividade->roUltimaAtividade->getOcorrenciaAtividade() );
            $this->obTCEMElementoAtivCadEconomico->setDado( "cod_elemento"         , $obRCEMElemento->getCodigoElemento() );
            $this->obTCEMElementoAtivCadEconomico->setDado( "ocorrencia_elemento"  , $obRCEMElemento->getOcorrenciaElemento() );
            $obErro = $this->obTCEMElementoAtivCadEconomico->alteracao( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
            if ( !$obErro->ocorreu() ) {
                $arTmp = $obRCEMElemento->getArrayElemento();
                foreach ($arTmp as $valor => $key) {
                    foreach ($key as $val => $chave) {
                        $obRCEMElemento->obRCadastroDinamico->addAtributosDinamicos( $val, $chave );
                        $arChaveAtributo =  array( "cod_elemento"        => $obRCEMElemento->getCodigoElemento(),
                                                   "inscricao_economica" => $this->getInscricaoEconomica(),
                                                   "cod_atividade"       => $this->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade(),
                                                   "ocorrencia_atividade"=> $this->roUltimaInscricaoAtividade->roUltimaAtividade->getOcorrenciaAtividade(),
                                                   "ocorrencia_elemento" => $obRCEMElemento->getOcorrenciaElemento() );

                        $obRCEMElemento->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                        $obErro = $obRCEMElemento->obRCadastroDinamico->alterarValores( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                    $obRCEMElemento->obRCadastroDinamico->setAtributosDinamicos( array() );

                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMElementoAtivCadEconomico );

    return $obErro;
}
/**
    * Lista os Elementos ativos da Inscricao Economica por Atividade
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementosAtivos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND ace.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    $stFiltro .= " AND ace.ativo = true ";

    $this->obTCEMElementoAtivCadEconomico = new TCEMElementoAtivCadEconomico;
    $obErro = $this->obTCEMElementoAtivCadEconomico->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
function consultarMaxOcorrenciaElemento(&$inMaxOcorrencia,  $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " inscricao_economica = ".$this->getInscricaoEconomica(). " AND";
    }
    if ( $this->roUltimoElemento->getCodigoElemento() ) {
        $stFiltro .= " cod_elemento = ".$this->roUltimoElemento->getCodigoElemento(). " AND";
    }
    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE  ".substr($stFiltro,0,-4);
    }
    $this->obTCEMElementoAtivCadEconomico = new TCEMElementoAtivCadEconomico;
    $obErro = $this->obTCEMElementoAtivCadEconomico->recuperaMaxOcorrenciaElemento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    if ( !$rsRecordSet->eof() ) {
        $inMaxOcorrencia = $rsRecordSet->getCampo("max_ocorrencia");
    }

    return $obErro;
}

function consultarNomeInscricaoEconomica(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND   CE.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    $obErro = $this->obTCEMCadastroEconomico->recuperaNomeEmpresa( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

function listaTiposDeBaixa(&$rsLista, $boTransacao = '')
{
    $stFiltro = "";
    if ( $this->getCodigoTipoDeBaixa() ) {
        $stFiltro .= " cod_tipo = ".$this->getCodigoTipoDeBaixa() . ' AND ';
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE  ".substr($stFiltro,0,-4);
    }

    $stOrdem = "";
    $obErro = $this->obTCEMTipoBaixaInscricao->recuperaTodos( $rsLista, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultaBaixaProcesso($boTransacao='')
{
    $obErro = new Erro;

    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoBaixaCadEconomico.class.php"      );
    $obTCEMBaixaProcesso = new TCEMProcessoBaixaCadEconomico;

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " pb.inscricao_economica = ".$this->getInscricaoEconomica() . ' AND ';
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE  ".substr($stFiltro,0,-4);
    }

    $obErro = $obTCEMBaixaProcesso->recuperaProcessoBaixa( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    if ( !$obErro->ocorreu () ) {
        $this->setCodProcessoBaixa    ( $rsRecordSet->getCampo('cod_processo') );
        $this->setExercicioBaixa ( $rsRecordSet->getCampo('exercicio') );
        $this->setDataBaixa ( $rsRecordSet->getCampo('dt_inicio') );
    }

    return $obErro;
}

/**
    * Lista os dados referentes a inscrições econômicas que podem ser convertidas para empresa de direito
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarInscricaoConversao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA = ".$this->getInscricaoEconomica();
    }

    if ( $this->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " AND COALESCE(CADASTRO_ECONOMICO_AUTONOMO.NUMCGM,CADASTRO_ECONOMICO_EMPRESA_FATO.NUMCGM)=".$this->obRCGMPessoaFisica->getNumCGM();
    }

    $stOrdem  = " ORDER BY CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA ";

    $obErro = $this->obTCEMCadastroEconomico->recuperaInscricaoEconomicaConversao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
