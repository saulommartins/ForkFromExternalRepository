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
    * Classe de regra de negócio para Empresa de Direito
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMEmpresaDeDireito.class.php 60957 2014-11-26 13:55:58Z michel $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.26  2007/07/23 15:34:12  cercato
Bug#9727#

Revision 1.25  2007/05/09 13:04:22  cercato
Bug #9247#

Revision 1.24  2007/01/22 15:58:46  cercato
Bug #8157#

Revision 1.23  2006/12/20 11:32:31  dibueno
Bug #7874#

Revision 1.22  2006/12/19 16:37:49  dibueno
Bug #7874#

Revision 1.21  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoEmpresaDireito.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoEmpresaFato.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoAutonomo.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMEmpresaDireitoNaturezaJuridica.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoEmpDireitoNatJuridica.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoEmpresaDireitoValor.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoEmpresaFatoValor.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoCadEconAutonomoValor.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMSociedade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php" );

/**
* Classe de regra de negócio para Empresa de Direito
* Data de Criação: 01/12/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMEmpresaDeDireito extends RCEMInscricaoEconomica
{
/**
* @access Private
* @var Object
*/
var $obTCEMCadastroEconomicoEmpresaDireito;
/**
* @access Private
* @var String
*/
var $stRegistroJuntaComercial;
/**
* @access Private
* @var Object
*/
var $roUltimoCategoria;
/**
* @access Private
* @var Object
*/
var $roUltimaSociedade;

/**
* @access Public
* @param String $valor
*/
function setRegistroJuntaComercial($valor)
{
    $this->stRegistroJuntaComercial = $valor;
}

/**
* @access Public
* @return String
*/
function getRegistroJuntaComercial()
{
    return $this->stRegistroJuntaComercial;
}

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMEmpresaDeDireito()
{
    parent::RCEMInscricaoEconomica();

    $this->obTCEMProcessoEmpDireitoNatJuridica = new TCEMProcessoEmpDireitoNatJuridica;
    $this->obTCEMCadastroEconomicoEmpresaDireito = new TCEMCadastroEconomicoEmpresaDireito;
    $this->obTCEMEmpresaDireitoNaturezaJuridica  = new TCEMEmpresaDireitoNaturezaJuridica;
    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCEMSociedade = new RCEMSociedade;
    $this->obRCEMNaturezaJuridica = new RCEMNaturezaJuridica;
    $this->obTransacao = new Transacao;
    $this->obRCEMCategoria = new RCEMCategoria;
    $this->obRCGMPessoaJuridica  = new RCGMPessoaJuridica;
    $this->arRCEMSociedade = array();
    $this->obRCadastroDinamico->setPersistenteValores ( new TCEMAtributoEmpresaDireitoValor );
    $this->obRCadastroDinamico->setCodCadastro ( 2 );
}

/**
    * Inclui os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirInscricao($boTransacao = "")
{
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ($obErro->ocorreu())
        return $obErro;

    $obErro = parent::incluirInscricao($boTransacao);
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
        $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "numcgm"             , $this->obRCGMPessoaJuridica->getNumCGM() );
        $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "num_registro_junta" , $this->getRegistroJuntaComercial() );
        $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "cod_categoria"      , $this->obRCEMCategoria->getCodigoCategoria() );
        
        $stFiltro = " AND coalesce( ef.numcgm, ed.numcgm, au.numcgm ) = ".$this->obRCGMPessoaJuridica->getNumCGM();
        $obErro = $this->obTCEMCadastroEconomico->recuperaInscricao($rsInscricao, $stFiltro, "", $boTransacao);

        if($rsInscricao->getNumLinhas()>0)
            $obErro->setDescricao("ERROR: CGM ".$this->obRCGMPessoaJuridica->getNumCGM()." pertencente a outra Inscrição Econômica. Contate suporte! ");

        if ( !$obErro->ocorreu() ) 
            $obErro = $this->obTCEMCadastroEconomicoEmpresaDireito->inclusao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $arChaveAtributoInscricao = array( "inscricao_economica" => $this->getInscricaoEconomica() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "cod_natureza"        , $this->obRCEMNaturezaJuridica->getCodigoNatureza() );
                $obErro = $this->obTCEMEmpresaDireitoNaturezaJuridica->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso()) {
                    $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("ano_exercicio", $this->getAnoExercicio());
                    $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("cod_processo", $this->getCodigoProcesso());
                    $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("inscricao_economica", $this->getInscricaoEconomica());

                    $this->obTCEMProcessoEmpDireitoNatJuridica->setDado( "timestamp", "('now'::text)::timestamp(3)");

                    $obErro = $this->obTCEMProcessoEmpDireitoNatJuridica->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    foreach ($this->arRCEMSociedade as $obRCEMSociedade) {
                        $obRCEMSociedade->setCodigoProcesso( $this->getCodigoProcesso() );
                        $obRCEMSociedade->setAnoExercicio( $this->getAnoExercicio() );
                        $obErro = $obRCEMSociedade->incluirSociedade($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

    return $obErro;
}

/**
    * Inclui os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function ConverterInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = parent::converterInscricao($boTransacao);
        if ( !$obErro->ocorreu() ) {

            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "numcgm"             , $this->obRCGMPessoaJuridica->getNumCGM() );
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "num_registro_junta" , $this->getRegistroJuntaComercial() );
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "cod_categoria"      , $this->obRCEMCategoria->getCodigoCategoria() );

            $obErro = $this->obTCEMCadastroEconomicoEmpresaDireito->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( !$obErro->ocorreu() ) {
                    $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                    $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "cod_natureza"        , $this->obRCEMNaturezaJuridica->getCodigoNatureza() );
                    $obErro = $this->obTCEMEmpresaDireitoNaturezaJuridica->inclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        foreach ($this->arRCEMSociedade as $obRCEMSociedade) {
                            $obErro = $obRCEMSociedade->incluirSociedade( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obRCEMEmpresaDeFato = new RCEMEmpresaDeFato; //verificando se a conversao eh de uma empresa de fato ou um autonomo
                        $obRCEMEmpresaDeFato->setInscricaoEconomica( $this->getInscricaoEconomica() );
                        $obErro = $obRCEMEmpresaDeFato->listarInscricao( $rsInscricao, $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            if ( $rsInscricao->eof() )
                                $boEmpresaDeFato = false; //nao eh empresa de fato
                            else
                                $boEmpresaDeFato = true; //eh empresa de fato

                            if ( !$obErro->ocorreu() ) {
                                //PASSA OS ATRIBUTOS DA TABELA EMPRESA FATO PARA EMPRESA DIREITO
                                $arChaveAtributoInscricao = array( "inscricao_economica" => $this->getInscricaoEconomica() );
                                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
                                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );

                                //REMOCAO DOS ATRIBUTOS
                                if ($boEmpresaDeFato == true) {
                                    $obTCEMAtributoEmpresaFatoValor = new TCEMAtributoEmpresaFatoValor;
                                    $obTCEMAtributoEmpresaFatoValor->setDado ( "valor" , $this->getInscricaoEconomica() );
                                    $obErro = $obTCEMAtributoEmpresaFatoValor->RemoveAtributoEmpresaFato( $boTransacao );
                                    //$obTCEMAtributoEmpresaFatoValor->debug();
                                } else {
                                    $obTCEMAtributoAutonomoValor = new TCEMAtributoCadEconAutonomoValor;
                                    $obTCEMAtributoAutonomoValor->setDado ( "valor" , $this->getInscricaoEconomica() );
                                    $obErro = $obTCEMAtributoAutonomoValor->RemoveAtributoAutonomo( $boTransacao );
                                }

                                //REMOCOES DO CADASTRO DE EMPRESA DE FATO
                                if ($boEmpresaDeFato == true) {
                                    $obTCEMEmpresaFato = new TCEMCadastroEconomicoEmpresaFato;
                                    $obTCEMEmpresaFato->setDado ('inscricao_economica', $this->getInscricaoEconomica() );
                                    $obErro = $obTCEMEmpresaFato->exclusao ( $boTransacao );
                                    //$obTCEMEmpresaFato->debug();
                                } else {
                                    $obTCEMAutonomo = new TCEMCadastroEconomicoAutonomo;
                                    $obTCEMAutonomo->setDado ('inscricao_economica', $this->getInscricaoEconomica() );
                                    $obErro = $obTCEMAutonomo->exclusao ( $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

    return $obErro;
}

/**
    * Altera os dados da Inscrição setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::alterarInscricao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "numcgm"             , $this->obRCGMPessoaJuridica->getNumCGM() );
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "num_registro_junta" , $this->getRegistroJuntaComercial() );
            $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "cod_categoria"      , $this->obRCEMCategoria->getCodigoCategoria() );
            $obErro = $this->obTCEMCadastroEconomicoEmpresaDireito->alteracao( $boTransacao );
            //$this->obTCEMCadastroEconomicoEmpresaDireito->debug();
            //exit;
            if ( !$obErro->ocorreu() ) {

                $arChaveAtributoInscricao = array ( "inscricao_economica" => $this->getInscricaoEconomica() );

                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
                $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );

                if ( !$obErro->ocorreu() ) {

                    $natureza = str_replace ( '-','', $this->obRCEMNaturezaJuridica->getCodigoNatureza() );
                    $stFiltroNatureza = " WHERE inscricao_economica = ".$this->getInscricaoEconomica();
                    $this->obTCEMEmpresaDireitoNaturezaJuridica->recuperaTodos( $rsListaNatureza, $stFiltroNatureza, " timestamp desc Limit 1 ", $boTransacao );
                    $boInclusao = true;
                    if ( !$rsListaNatureza->Eof() ) {
                        if ( $rsListaNatureza->getCampo("cod_natureza") == $natureza ) {
                            $boInclusao = false;
                        }
                    }
                    $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
                    $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "cod_natureza" , $natureza );
                    if ( $boInclusao )
                        $obErro = $this->obTCEMEmpresaDireitoNaturezaJuridica->inclusao( $boTransacao );
                    //$this->obTCEMEmpresaDireitoNaturezaJuridica->debug();
//                    exit;

                    if ( $boInclusao && !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso()) {
                        $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("ano_exercicio", $this->getAnoExercicio());
                        $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("cod_processo", $this->getCodigoProcesso());
                        $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("inscricao_economica", $this->getInscricaoEconomica());

                        $obErro = $this->obTCEMProcessoEmpDireitoNatJuridica->inclusao( $boTransacao );
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

    return $obErro;
}

/**
    * Excluir os dados da Inscrição setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirInscricao($boTransacao = "")
{
    include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
    $obTARRCEFaturamento = new TARRCadastroEconomicoFaturamento;
    $obTARRCEFaturamento->setDado ( 'inscricao_economica', $this->getInscricaoEconomica() );

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro = " WHERE CEF.inscricao_economica =  ".$this->getInscricaoEconomica();
        $obTARRCEFaturamento->recuperaTimestampCadastroEconomicoFaturamento( $rsLista, $stFiltro, $stOrdem, $boTransacao );
    } else
        $rsLista = new Recordset;

    if ( !$rsLista->eof() ) {
        $obErro = new Erro;
        $obErro->setDescricao( "Inscrição Econômica (".$this->getInscricaoEconomica().") apresenta lançamentos contábeis. Não pode ser excluída!" );

        return $obErro;
    }

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $this->obTCEMProcessoEmpDireitoNatJuridica->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $this->obTCEMProcessoEmpDireitoNatJuridica->setDado( "timestamp", "");
    $obErro = $this->obTCEMProcessoEmpDireitoNatJuridica->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( "inscricao_economica" , $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMEmpresaDireitoNaturezaJuridica->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $arChaveAtributoInscricao = array ( "inscricao_economica" => $this->getInscricaoEconomica() );
    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
    $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $obErro = $this->roUltimaSociedade->excluirSociedade( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    $this->obTCEMCadastroEconomicoEmpresaDireito->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
    $obErro = $this->obTCEMCadastroEconomicoEmpresaDireito->exclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

        return $obErro;
    }

    if ( !$obErro->ocorreu() )
        $obErro = parent::excluirInscricao( $boTransacao );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

    return $obErro;
}

/**
    * Adiciona objetos de Pessoa Jurídica
    * @access Public
*/
function addSociedade()
{
    $this->arRCEMSociedade[] = new RCEMSociedade( $this );
    $this->roUltimaSociedade = &$this->arRCEMSociedade[ count( $this->arRCEMSociedade ) - 1 ];
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
        $stFiltro .= " and ce.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    if ( $this->obRCGMPessoaJuridica->getNumCGM() ) {
        $stFiltro .= " and cgm.numcgm = ".$this->obRCGMPessoaJuridica->getNumCGM();
    }
    /*if ( $this->obRCGMPessoaJuridica->getNumCGM() ) {
        $stFiltro .= " and ".$this->obRCGMPessoaJuridica->getNumCGM()." = any ( economico.fn_busca_sociedade(ce.inscricao_economica))";
    } as buscas só serão realizadas pelos CGMS das empresas, e não dos sócios */

    $stOrdem  = " order by ce.inscricao_economica ";

    $obErro = $this->obTCEMCadastroEconomicoEmpresaDireito->recuperaInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    //$this->obTCEMCadastroEconomicoEmpresaDireito->debug();
    return $obErro;
}

/**
    * Lista a natureza jurídica da inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEmpresaDireitoNatureza(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->obRCEMNaturezaJuridica->getCodigoNatureza() ) {
        $stFiltro .= " and nj.cod_natureza = ".$this->obRCEMNaturezaJuridica->getCodigoNatureza();
    }

    if ( $this->obRCEMNaturezaJuridica->getNomeNatureza() ) {
        $stFiltro .= " and nj.nom_natureza = ".$this->obRCEMNaturezaJuridica->getNomeNatureza();
    }

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " and enj.inscricao_economica = ".$this->getInscricaoEconomica();
    }

    $stOrdem = " ORDER BY enj.inscricao_economica ";
    $obErro = $this->obTCEMEmpresaDireitoNaturezaJuridica->recuperaEmpresaDireitoNatureza( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//              $this->obTCEMEmpresaDireitoNaturezaJuridica->debug();
return $obErro;
}

/**
    * Lista a natureza jurídica da inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEmpresaDireitoSociedade(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND SO.INSCRICAO_ECONOMICA = ".$this->getInscricaoEconomica();
    }

    if ( $this->obRCEMSociedade->obRCGM->getNumCgm() ) {
        $stFiltro .= " AND SO.NUMCGM = ".$this->roUltimaSociedade->obRCGM->getNumCgm();
    }

    $stOrdem = " ORDER BY SO.NUMCGM ";
    $obErro = $this->obRCEMSociedade->obTCEMSociedade->recuperaSociedadeInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Altera os dados de Natureza Jurídica da Inscrição setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarEmpresaDireitoNatureza($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( 'cod_natureza', $this->obRCEMNaturezaJuridica->getCodigoNatureza() );
        $this->obTCEMEmpresaDireitoNaturezaJuridica->setDado( 'inscricao_economica', $this->getInscricaoEconomica() );
        $obErro = $this->obTCEMEmpresaDireitoNaturezaJuridica->alteracao( $boTransacao );
//$this->obTCEMEmpresaDireitoNaturezaJuridica->debug();exit();
        if ( !$obErro->ocorreu() && $this->getAnoExercicio() && $this->getCodigoProcesso()) {
            $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("ano_exercicio", $this->getAnoExercicio());
            $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("cod_processo", $this->getCodigoProcesso());
            $this->obTCEMProcessoEmpDireitoNatJuridica->setDado("inscricao_economica", $this->getInscricaoEconomica());

            $obErro = $this->obTCEMProcessoEmpDireitoNatJuridica->inclusao( $boTransacao );
//$this->obTCEMProcessoEmpDireitoNatJuridica->debug();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaDireito );

    return $obErro;
}

/**
    * Altera os dados de Sociedade da Inscrição setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarEmpresaDireitoSociedade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $rsSocios = new Recordset;
    if ( !$obErro->ocorreu() ) {
         $obErro = $this->listarEmpresaDireitoSociedade( $rsSocios , $boTransacao );
         if ( !$obErro->ocorreu() ) {
            foreach ($this->arRCEMSociedade as $obRCEMSociedade) {
                $obRCEMSociedade->setCodigoProcesso( $this->getCodigoProcesso() );
                $obRCEMSociedade->setAnoExercicio( $this->getAnoExercicio() );
                $obRCEMSociedade->obTCEMSociedade->setDado( "numcgm", $obRCEMSociedade->obRCGM->getNumCGM());
                $obRCEMSociedade->obTCEMSociedade->setDado( "inscricao_economica", $this->getInscricaoEconomica());
                $obRCEMSociedade->obTCEMSociedade->setDado( "quota_socio", $obRCEMSociedade->getQuotaSocios());
                $obErro = $obRCEMSociedade->obTCEMSociedade->inclusao( $boTransacao );
                if ($obErro->ocorreu())
                    break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRCEMSociedade->obTCEMSociedade );

    return $obErro;
}

}
