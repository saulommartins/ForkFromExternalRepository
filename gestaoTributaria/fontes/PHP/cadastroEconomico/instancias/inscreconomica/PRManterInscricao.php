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
    * Página de Processamento para cadastro Inscrição Econômica
    * Data de Criação   : 03/01/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRManterInscricao.php 62193 2015-04-06 19:03:02Z jean $

    * Casos de uso: uc-05.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php"      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php"           );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgRelat    = "FM".$stPrograma."Emissao.php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
$pgDefAtiv  = "FMDefinirAtividades.php";

switch ($_REQUEST[ "inCodigoEnquadramento" ]) {
    case 1:
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeFato;
    break;
    case 2:
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
    break;
    case 3:
        $obRCEMInscricaoEconomica = new RCEMAutonomo;
    break;
}

$obErro = new Erro;
$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

$obMascara = new Mascara;
switch ($stAcao) {
    case "reativar" :
     $dtTermino = explode("/",$_REQUEST['dtDataTermino']);
     $dtTermino = $dtTermino[2].$dtTermino[1].$dtTermino[0];
     $dtAtual   = date('Ymd');
     if ($dtTermino > $dtAtual) {
        $obErro->setDescricao("Data de término deve ser igual ou inferior a data atual.");
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_ativar","erro");
        break;
     }
     if (!(sistemaLegado::comparaDatas( $_REQUEST['stDtInicio'], $_REQUEST['dtDataTermino'] ))) {

      $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica();
      $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
      $obRCEMInscricaoEconomica->setDataBaixa  ($_REQUEST['stDtInicio']    );
      $obRCEMInscricaoEconomica->setDataTermino($_REQUEST['dtDataTermino'] );
      $obErro = $obRCEMInscricaoEconomica->reativarCadastroEconomico();
      if ( !$obErro->ocorreu() ) {
          sistemaLegado::alertaAviso($pgList,"Reativar inscrição econômica concluída com sucesso! Inscrição: ".$_REQUEST['inInscricaoEconomica'],"cc","aviso",Sessao::getId(),"../");
      } else {
          sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_reativar","erro");
      }
     } else {
        $obErro->setDescricao( 'Data de término deve ser posterior à data de baixa da Inscrição Econômica.' );
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_ativar","erro");
     }
    break;
    case "incluir" :
        $obErro = VerificaDadosEndereco();
        if ( !$obErro->ocorreu() ) {
            $arNumeroInscricao = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST['inNumeroInscricao']);
            $inNumInscricao = "";
            foreach ($arNumeroInscricao as $valor) {
                $inNumInscricao .= $valor;
            }

            $obRCEMInscricaoEconomica->obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $obRCEMInscricaoEconomica->obRCEMConfiguracao->consultarConfiguracao();
            $boNumeroInscricao = $obRCEMInscricaoEconomica->obRCEMConfiguracao->getNumeroInscricao();
            if ($boNumeroInscricao == "t") {
                $obErro = $obRCEMInscricaoEconomica->obTCEMCadastroEconomico->proximoCod( $inCodigoInscricao );
                $obRCEMInscricaoEconomica->setInscricaoEconomica( $inCodigoInscricao );
            } else {
                $obRCEMInscricaoEconomica->setInscricaoEconomica( $inNumInscricao );
            }

    //-----------------------------------------------------------------------------------------------------
            if ($_REQUEST['inNumProcesso']) {
                list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
                $obRCEMInscricaoEconomica->setCodigoProcesso( $inProcesso );
                $obRCEMInscricaoEconomica->setAnoExercicio( $inExercicio );
            }
    //-----------------------------------------------------------------------------------------------------

            if ($_REQUEST['boTipoDomicilio'] == 'IC') { //imovel jah cadastrado

                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioFiscal ($_REQUEST[ 'inCodigoDomicilio' ]);

            } else {

                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setNumero       ($_REQUEST['inNumero']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setComplemento  ($_REQUEST['stComplemento']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodLogradouro($_REQUEST['inNumLogradouro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodBairro    ($_REQUEST['inCodigoBairro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodMunicipio ($_REQUEST['inCodMunicipio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodUF        ($_REQUEST['inCodUF']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCaixaPostal  ($_REQUEST['stCaixaPostal']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCEP          ($_REQUEST['cmbCEP']);

            }
    //-----------------------------------------------------------------------------------------------------
            $obRCEMInscricaoEconomica->setDataAbertura                       ( $_REQUEST[ 'stDtAbertura']            );

            $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setSequencia( $_REQUEST['inSequencia'] );
            $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setNumCGM( $_REQUEST['inNumCGMResponsavel'] );

            $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->obRProfissao->setCodigoProfissao( $_REQUEST[ 'inCodProfissao' ] );
            switch ($_REQUEST[ "inCodigoEnquadramento" ]) {
                case 1:
                case 3:
                    $obRCEMInscricaoEconomica->obRCGMPessoaFisica->setNumCGM   ( $_REQUEST[ 'inNumCGM' ]  );
                break;
                case 2:
                    $inNatureza = str_replace( "-" ,  "" , $_REQUEST[ 'inCodigoNatureza'] );
                    $obRCEMInscricaoEconomica->setRegistroJuntaComercial   ( $_REQUEST[ 'inRegistroJunta'  ] );
                    if (!$inNatureza) {
                        $obErro->setDescricao("Natureza Jurídica deve ser setada!");
                    } else {
                        $obRCEMInscricaoEconomica->obRCEMNaturezaJuridica->setCodigoNatureza ( $inNatureza );
                    }
                    $obRCEMInscricaoEconomica->obRCGMPessoaJuridica->setNumCGM   ( $_REQUEST[ 'inNumCGM'  ]    );
                    $obRCEMInscricaoEconomica->obRCEMCategoria->setCodigoCategoria  ( $_REQUEST['stCategoria'] );
                    $arSociosSessao = Sessao::read( "socios" );
                    if ( count( $arSociosSessao ) != 0 ) {
                        foreach ($arSociosSessao as $inChave => $arSocios) {
                            $obRCEMInscricaoEconomica->addSociedade();
                            $obRCEMInscricaoEconomica->roUltimaSociedade->addInscricao( $obRCEMInscricaoEconomica );
                            $obRCEMInscricaoEconomica->roUltimaSociedade->obRCGM->setNumCGM ( $arSocios['inCodigoSocio'] );
                            $obRCEMInscricaoEconomica->roUltimaSociedade->setQuotaSocios    ( $arSocios['flQuota']       );
                        }
                    } else {
                        $obErro->setDescricao( "É necessário a inclusão de pelo menos um sócio." );
                    }
                break;
            }
            // dados de atributos dinamicos
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCEMInscricaoEconomica->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            if ( !$obErro->ocorreu() ) {
                $obErro = $obRCEMInscricaoEconomica->incluirInscricao();
                if ( $obErro->ocorreu() ) {
                    $obErro->setDescricao("CGM ".$obRCEMInscricaoEconomica->obTCEMCadastroEconomicoEmpresaDireito->arEstrutura[1]->getConteudo()." pertencente a outra Inscrição Econômica.");
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boSegueAtividade']) {
                $pgProx  = $pgDefAtiv."?inCodigoEnquadramento=".$_REQUEST['inCodigoEnquadramento']."&inInscricaoEconomica=".$obRCEMInscricaoEconomica->getInscricaoEconomica()."&stDtAbertura=".$_REQUEST["stDtAbertura"];
                $pgProx .= "&stAcao=def_ativ&stDescQuestao=".$obRCEMInscricaoEconomica->getInscricaoEconomica()."&inCGM=".$_REQUEST['inNumCGM'];
            } else {
                $pgProx = $pgForm;
            }
            sistemaLegado::alertaAviso($pgProx,"Inscrição econômica: ".$obRCEMInscricaoEconomica->getInscricaoEconomica() ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obErro = new Erro;
        $obRCEMInscricaoEconomica->setInscricaoEconomica                 ( $_REQUEST[ 'inInscricaoEconomica'] );
        $obRCEMInscricaoEconomica->setDataAbertura                       ( $_REQUEST[ 'stDtAbertura'] );
        $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setNumCGM   ( $_REQUEST[ 'inNumCGMResponsavel']  );
        $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setSequencia( $_REQUEST['inSequencia'] );
        $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->obRProfissao->setCodigoProfissao( $_REQUEST[ 'inCodProfissao']       );

        $obErro = VerificaDadosEndereco();
        if ( !$obErro->ocorreu() ) {

            if ($_REQUEST['boTipoDomicilio'] == 'IC') { //alteração -> novo endereço fiscal é Cadastrado

                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioFiscal ($_REQUEST[ 'inCodigoDomicilio' ]);

            } elseif ($_REQUEST['boTipoDomicilio'] == 'EI') { //alteração -> novo domicilio fiscal é um endereço informado

                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setNumero       ($_REQUEST['inNumero']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setComplemento  ($_REQUEST['stComplemento']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodLogradouro($_REQUEST['inNumLogradouro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodBairro    ($_REQUEST['inCodigoBairro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodMunicipio ($_REQUEST['inCodMunicipio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodUF        ($_REQUEST['inCodUF']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCaixaPostal  ($_REQUEST['stCaixaPostal']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCEP          ($_REQUEST['cmbCEP']);
            }

            switch ($_REQUEST[ "inCodigoEnquadramento" ]) {
                case 1:
                case 3:
                    $obRCEMInscricaoEconomica->obRCGMPessoaFisica->setNumCGM( $_REQUEST[ 'inNumCGM' ] );
                break;
                case 2:
                    $obRCEMInscricaoEconomica->setRegistroJuntaComercial                 ( $_REQUEST[ 'inRegistroJunta']  );
                    $obRCEMInscricaoEconomica->obRCEMNaturezaJuridica->setCodigoNatureza ( $_REQUEST[ 'inCodigoNatureza'] );
                    $obRCEMInscricaoEconomica->obRCGMPessoaJuridica->setNumCGM           ( $_REQUEST[ 'inNumCGM']         );
                    $obRCEMInscricaoEconomica->obRCEMCategoria->setCodigoCategoria       ( $_REQUEST[ 'stCategoria']      );
                break;
            }

 //-----------------------------------------------------------------------------------------------------
            if ($_REQUEST['inNumProcesso']) {
                list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
                $obRCEMInscricaoEconomica->setCodigoProcesso( $inProcesso );
                $obRCEMInscricaoEconomica->setAnoExercicio( $inExercicio );
            }
    //-----------------------------------------------------------------------------------------------------

            // dados de atributos dinamicos
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }

                $obRCEMInscricaoEconomica->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo ,$value);
            }

            $obErro = $obRCEMInscricaoEconomica->alterarInscricao();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList.$stLink,"Inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRCEMInscricaoEconomica->setInscricaoEconomica ( $_REQUEST['inInscricaoEconomica'] );
        if ($_REQUEST['inCodigoEnquadramento'] == 2) {
            $obRCEMInscricaoEconomica->addSociedade();
            $obRCEMInscricaoEconomica->roUltimaSociedade->addInscricao( $obRCEMInscricaoEconomica );
        }

        $obRCEMInscricaoEconomica->addInscricaoAtividade();
        $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->addAtividade();
        $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $arValor['inCodigoAtividade'] );

        $obErro = $obRCEMInscricaoEconomica->excluirInscricao();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList, "Inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
            #sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
    case "deoficio":
    case "baixar":

        $dtBaixa = explode("/",$_REQUEST['dtDataBaixa']);
        $dtBaixa = $dtBaixa[2].$dtBaixa[1].$dtBaixa[0];
        $dtAtual   = date('Ymd');
        if ($dtBaixa > $dtAtual) {
           $obErro->setDescricao("Data de baixa deve ser igual ou inferior a data atual.");
           sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_ativar","erro");
        }
        $x = explode ('/', $_REQUEST['inCodProcesso']);

        $obRCEMInscricaoEconomica->setCodProcessoBaixa ( $x[0] );
        $obRCEMInscricaoEconomica->setExercicioBaixa ( $x[1] );

        $obRCEMInscricaoEconomica->setInscricaoEconomica ( $_REQUEST['inInscricaoEconomica'] );
        $obRCEMInscricaoEconomica->setDataBaixa          ( $_REQUEST['dtDataBaixa']          );
        $obRCEMInscricaoEconomica->setMotivoBaixa        ( $_REQUEST['stMotivo']        );
        $obRCEMInscricaoEconomica->setCodigoTipoDeBaixa  ( $_REQUEST['cmbTipo'] );
        if ( Sessao::read('acao') == 451 ) {
            $obRCEMInscricaoEconomica->setDeOficio( false );
        } elseif ( Sessao::read('acao') == 452 ) {
            $obRCEMInscricaoEconomica->setDeOficio( true  );
        }

        if ( !sistemaLegado::comparaDatas( $_REQUEST['dtDataBaixa'], $_REQUEST['stDtAbertura'] ) ) {
            $obErro->setDescricao( 'Data de baixa deve ser posterior à data de inclusão da Inscrição Econômica.' );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMInscricaoEconomica->baixarInscricao();
        }

        if ( !$obErro->ocorreu() ) {
            $obRCEMConfiguracao = new RCEMConfiguracao;
            $obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $obRCEMConfiguracao->consultarConfiguracao();
            if ( $obRCEMConfiguracao->getEmissaoCertidaoBaixa() == "sim" ) {
                $pgRelat .= "?stAcao=baixa&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"]."&cmbDocumento=".$_REQUEST["cmbDocumento"];
                sistemaLegado::alertaAviso( $pgRelat,"Inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"baixar","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::alertaAviso($pgList,"Inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"baixar","aviso", Sessao::getId(), "../");
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;
    case "natureza":
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
        $inCodNatureza = str_replace("-","",$_REQUEST[ 'inCodigoNatureza' ]);
        $obRCEMInscricaoEconomica->obRCEMNaturezaJuridica->setCodigoNatureza( $inCodNatureza );
        $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );

        //-----------------------------------------------------------------------------------------------------
        if ($_REQUEST['inNumProcesso']) {
            list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
            $obRCEMInscricaoEconomica->setCodigoProcesso( $inProcesso );
            $obRCEMInscricaoEconomica->setAnoExercicio( $inExercicio );
        }
        //-----------------------------------------------------------------------------------------------------

        $obErro = $obRCEMInscricaoEconomica->alterarEmpresaDireitoNatureza();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Alterar Natureza Jurídica concluído com sucesso! (Natureza Jurídica: ".$_REQUEST['inCodigoNatureza'].")","natureza","alterar", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_natureza","erro");
        }
    break;
    case "domicilio":
        $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
        $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );

        $obErro = VerificaDadosEndereco();
        if ( !$obErro->ocorreu() ) {
            //-----------------------------------------------------------------------------------------------------
            if ($_REQUEST['inNumProcesso']) {
                list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
                $obRCEMInscricaoEconomica->setCodigoProcesso( $inProcesso );
                $obRCEMInscricaoEconomica->setAnoExercicio( $inExercicio );
            }
            //-----------------------------------------------------------------------------------------------------
            if ($_REQUEST['boTipoDomicilio'] == 'IC') { //alteração -> novo endereço fiscal é Cadastrado
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioFiscal ($_REQUEST[ 'inCodigoDomicilio' ]);
            } elseif ($_REQUEST['boTipoDomicilio'] == 'EI') { //alteração -> novo domicilio fiscal é um endereço informado

                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setNumero       ($_REQUEST['inNumero']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setComplemento  ($_REQUEST['stComplemento']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodLogradouro($_REQUEST['inNumLogradouro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodBairro    ($_REQUEST['inCodigoBairro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodMunicipio ($_REQUEST['inCodMunicipio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodUF        ($_REQUEST['inCodUF']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCaixaPostal  ($_REQUEST['stCaixaPostal']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCEP          ($_REQUEST['cmbCEP']);
            }

            $obErro = $obRCEMInscricaoEconomica->alterarInscricaoDomicilio();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Domicílio Fiscal alterado com sucesso [". $_REQUEST['stDescQuestao'].']',"domicilio","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_domicilio","erro");
        }
    break;

    case 'converter':

        $obErro = VerificaDadosEndereco();
        if ( !$obErro->ocorreu() ) {
            $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
            $arNumeroInscricao = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST['inNumeroInscricao']);
            $inNumInscricao = "";
            foreach ($arNumeroInscricao as $valor) {
                $inNumInscricao .= $valor;
            }
            $obRCEMInscricaoEconomica->setInscricaoEconomica( $inNumInscricao );
            //----------------------------------------------------------------------------- ENDEREÇO
            if ($_REQUEST['boTipoDomicilio'] == 'IC') { //imovel jah cadastrado
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioFiscal ($_REQUEST[ 'inCodigoDomicilio' ]);
            } else {
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setDomicilioExibir ($_REQUEST['boTipoDomicilio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setNumero       ($_REQUEST['inNumero']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setComplemento  ($_REQUEST['stComplemento']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodLogradouro($_REQUEST['inNumLogradouro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodBairro    ($_REQUEST['inCodigoBairro']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodMunicipio ($_REQUEST['inCodMunicipio']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCodUF        ($_REQUEST['inCodUF']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCaixaPostal  ($_REQUEST['stCaixaPostal']);
                $obRCEMInscricaoEconomica->obRCEMDomicilio->setCEP          ($_REQUEST['cmbCEP']);
            }
        //-------------------------------------------------------------------------------- ENDEREÇO FIM

        //--------------------------------------------------------------------------------------- DADOS PADRÃO
            $inNatureza = str_replace( "-" ,  "" , $_REQUEST[ 'inCodigoNatureza'] );
            $obRCEMInscricaoEconomica->setRegistroJuntaComercial   ( $_REQUEST[ 'inRegistroJunta'  ] );
            if (!$inNatureza) {
                $obErro->setDescricao("Natureza Jurídica deve ser setada!");
            } else {
                $obRCEMInscricaoEconomica->obRCEMNaturezaJuridica->setCodigoNatureza ( $inNatureza );
            }
            $obRCEMInscricaoEconomica->obRCGMPessoaJuridica->setNumCGM   ( $_REQUEST[ 'inNumCGMJuridica2'  ]    );
            $obRCEMInscricaoEconomica->obRCEMCategoria->setCodigoCategoria  ( $_REQUEST['stCategoria'] );
            $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setNumCGM( $_REQUEST[ 'inNumCGMResponsavel' ]    );
            $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->setSequencia( $_REQUEST['inSequencia'] );
            $obRCEMInscricaoEconomica->obRCEMResponsavelTecnico->obRProfissao->setCodigoProfissao( $_REQUEST[ 'inCodProfissao' ] );
    //--------------------------------------------------------------------------------------- DADOS PADRÃO FIM

    //--------------------------------------------------------------------------------------- SOCIOS
            $arSociosSessao = Sessao::read( "socios" );
            if ( count( $arSociosSessao ) != 0 ) {
                foreach ($arSociosSessao as $inChave => $arSocios) {
                    $obRCEMInscricaoEconomica->addSociedade();
                    $obRCEMInscricaoEconomica->roUltimaSociedade->addInscricao( $obRCEMInscricaoEconomica );
                    $obRCEMInscricaoEconomica->roUltimaSociedade->obRCGM->setNumCGM ( $arSocios['inCodigoSocio'] );
                    $obRCEMInscricaoEconomica->roUltimaSociedade->setQuotaSocios    ( $arSocios['flQuota']       );
                }
            } else {
                $obErro->setDescricao( "É necessário a inclusão de pelo menos um sócio." );
            }
    //--------------------------------------------------------------------------------------- SOCIOS FIM

    //--------------------------------------------------------------------------------------- ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCEMInscricaoEconomica->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
    //--------------------------------------------------------------------------------------- ATRIBUTOS

            if ( !$obErro->ocorreu() ) {
                $obErro = $obRCEMInscricaoEconomica->ConverterInscricao();
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boSegueAtividade']) {
                $pgProx  = $pgDefAtiv."?inCodigoEnquadramento=".$_REQUEST['inCodigoEnquadramento']."&inInscricaoEconomica=".$obRCEMInscricaoEconomica->getInscricaoEconomica()."&stDtAbertura=".$_REQUEST["stDtAbertura"];
                $pgProx .= "&stAcao=def_ativ&stDescQuestao=".$obRCEMInscricaoEconomica->getInscricaoEconomica()."&inCGM=".$_REQUEST['inNumCGM'];
            } else {
                $pgProx = $pgList;
            }
            sistemaLegado::alertaAviso($pgProx,"Inscrição econômica: ".$obRCEMInscricaoEconomica->getInscricaoEconomica() ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
}

function VerificaDadosEndereco()
{
    $obErro = new Erro;
    if ($_REQUEST['boTipoDomicilio'] == 'IC') {

        if ($_REQUEST['inCodigoDomicilio'] == '') {
            $obErro->setDescricao ( "Campo Domicílio Fiscal com valor inválido" );
        }

    } elseif ($_REQUEST['boTipoDomicilio'] == 'EI') {

        if ($_REQUEST['inNumero'] == '') {
            $obErro->setDescricao ( "Campo Número com valor inválido" );
        } else
        if ($_REQUEST['inCodigoBairro'] == '') {
            $obErro->setDescricao ( "Campo Bairro com valor inválido" );
        } else
        if ($_REQUEST['cmbCEP'] == '') {
            $obErro->setDescricao ( "Campo CEP com valor inválido" );
        } else
        if ($_REQUEST['inNumLogradouro'] == '') {
            $obErro->setDescricao ( "Campo Logradouro com valor inválido" );
        }

    }

    return $obErro;
}

?>
