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
 * Formulario de Convenio
 * Data de Criação   : 03/10/2006

 * @author Analista:
 * @author Desenvolvedor:  Lucas Teixeira Stephanou
 * @ignore

 $Id: FMManterConvenios.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

 *Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_COMPONENTES."IPopUpEditObjeto.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoConvenio.class.php";
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';



$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenios";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgFormConsulta = "FMConsultaConvenios";
$pgFormAnular   = "FMAnularConvenios";

include_once $pgJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

/* inicia as variáeis boleanas */
$boRescindir = false;
$boAlterar = false;
$boCampoLabel = false;
$boAlterarRescisao = false;

/*
    inicia as variáveis para passar no setValue dos campos
    caso entre nas condições, receberão valor para ser exibido na tela
*/

$dtRescisao = "";
$inCgmResponsavelJuridico = "";
$stNomCgm = "";
$vlMulta = "";
$vlIndenizacao = "";
$stMotivo = "";
$inNumRescisao = "";
$inExercicioRescisao = "";

$cod_uf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());

$obHdnCodUf= new Hidden ();
$obHdnCodUf->setId( 'inCodUF' );
$obHdnCodUf->setName( 'inCodUF' );
$obHdnCodUf->setValue( $cod_uf );

if ( ( $stAcao == 'alterar' ) || ( $stAcao == "rescindir" ) ) {
    $boCampoLabel = true;
    ($stAcao == 'alterar' ? $boAlterar = true : $boRescindir = true );

    require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoConvenio.class.php') ;
    $obConvenio = new TLicitacaoConvenio;
    $stFiltro = " AND  convenio.num_convenio = ".$_REQUEST[ 'inNumConvenio' ]." " ;
    $stFiltro .= "\n AND  convenio.exercicio = '".$_REQUEST[ 'inExercicio' ]."' " ;
    $obConvenio->recuperaRelacionamento ( $rsConvenio , $stFiltro , ' convenio.num_convenio');

    if ($boRescindir) {
        require_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoConvenioAditivos.class.php');
        $obConvenioAditivo = new TLicitacaoConvenioAditivos;
        $obConvenioAditivo->setDado("num_convenio", $_REQUEST['inNumConvenio']);
        $obConvenioAditivo->setDado("exercicio_convenio", $_REQUEST['inExercicio']);
        $obConvenioAditivo->recuperaMaximaAditivo($rsConvenioAditivo);

        require_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoRescisaoConvenio.class.php');
        $obRescisaoConvenio = new TLicitacaoRescisaoConvenio;
        $obRescisaoConvenio->setDado("num_convenio", $_REQUEST['inNumConvenio']);
        $obRescisaoConvenio->setDado("exercicio_convenio", $_REQUEST['inExercicio']);
        $obRescisaoConvenio->recuperaMontaRecuperaDadosRescisao($rsRescisaoConvenio);

        if (!empty($rsRescisaoConvenio->arElementos)) {
            $boAlterarRescisao = true;
        }

        $dtRescisao = $rsRescisaoConvenio->getCampo("dt_rescisao");
        $inCgmResponsavelJuridico = $rsRescisaoConvenio->getCampo("responsavel_juridico");
        $stNomCgm = $rsRescisaoConvenio->getCampo("nom_cgm");
        $vlMulta = $rsRescisaoConvenio->getCampo("vlr_multa");
        $vlIndenizacao = $rsRescisaoConvenio->getCampo("vlr_indenizacao");
        $stMotivo = $rsRescisaoConvenio->getCampo("motivo");
        $inNumRescisao = $rsRescisaoConvenio->getCampo("num_rescisao");
        $inExercicioRescisao = $rsRescisaoConvenio->getCampo("exercicio");
    }
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( 'oculto');

$obHdnInExercicio =  new Hidden;
$obHdnInExercicio->setName   ( "inExercicio"            );
$obHdnInExercicio->setValue  ( $request->get('inExercicio') );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );

if ($boCampoLabel == true) {

    if ($boRescindir == true) {
        /* EXERCICIO DO CONVENIO*/
        $obLblExercicio = new Label   ();
        $obLblExercicio->setRotulo    ( "Exercício do Convênio"  );
        $obLblExercicio->setValue     ( $rsConvenio->getCampo('exercicio') );

        $obHdnExercicio = new Hidden ();
        $obHdnExercicio->setName( 'inExercicio' );
        $obHdnExercicio->setValue( $rsConvenio->getCampo('exercicio') );

        /*
            data do maior aditivo cadastrado na tabela convenio_aditivo
            usada para a verificação da data de rescisão, onde ela não pode ser anterior a dtMaiorAditivo
        */
        $obHdnDtMaiorAditivo = new Hidden ();
        $obHdnDtMaiorAditivo->setId( 'dtMaiorAditivo' );
        $obHdnDtMaiorAditivo->setName( 'dtMaiorAditivo' );
        $obHdnDtMaiorAditivo->setValue( $rsConvenioAditivo->getCampo('dt_assinatura') );

        /* Se já existe uma recisão, irá apresentar o numero da rescisão na interface */
        if ($boAlterarRescisao) {

            /* Numero da Rescisão */
            $obLblNumRescisao = new Label   ();
            $obLblNumRescisao->setRotulo    ( "Número da Rescisão" );
            $obLblNumRescisao->setValue     ( $inNumRescisao );

            $obHdnNumRescisao = new Hidden ();
            $obHdnNumRescisao->setId   ( 'inNumRescisao' );
            $obHdnNumRescisao->setName ( 'inNumRescisao' );
            $obHdnNumRescisao->setValue( $inNumRescisao  );

            /*
                campo rescisao_convenio.exercicio
                precisa ser passado para poder usar para alterar os dados da rescisão do convenio
            */
            $obHdnExercicioRescisao = new Hidden ();
            $obHdnExercicioRescisao->setId( 'inExercicioRescisao' );
            $obHdnExercicioRescisao->setName( 'inExercicioRescisao' );
            $obHdnExercicioRescisao->setValue( $inExercicioRescisao );

        }
    }

    /* NUMERO DO CONVENIO*/
    $obLblNumConvenio = new Label	();
    $obLblNumConvenio->setRotulo	( "Número do Convênio"  );
    $obLblNumConvenio->setValue 	( $rsConvenio->getCampo('num_convenio') );

    $obHdnNumConvenio = new Hidden ();
    $obHdnNumConvenio->setName( 'inNumConvenio' );
    $obHdnNumConvenio->setValue( $rsConvenio->getCampo('num_convenio') );

    /* TIPO DE CONVENIO */
    $obLblTipoConvenio = new Label ();
    $obLblTipoConvenio->setRotulo   ( "Tipo de Convênio" );
    $obLblTipoConvenio->setValue    ( $rsConvenio->getCampo('cod_tipo_convenio').' - '.$rsConvenio->getCampo('descricao_tipo') );

    $obHdnCodTipoConvenio = New Hidden;
    $obHdnCodTipoConvenio->setName  ( 'inCodTipoConvenio'  );
    $obHdnCodTipoConvenio->setId    ( 'inCodTipoConvenio'  );
    $obHdnCodTipoConvenio->setValue ( $rsConvenio->getCampo('cod_tipo_convenio') );

    /* OBJETO */
    $obLblObjeto = new Label();
    $obLblObjeto->setRotulo  ( 'Objeto' );
    $obLblObjeto->setValue   ( $rsConvenio->getCampo( 'cod_objeto' ).' - '.$rsConvenio->getCampo( 'descricao_objeto' ) );

    $obHdnObjeto = new Hidden ;
    $obHdnObjeto->setName  ( 'stObjeto' );
    $obHdnObjeto->setId    ( 'stObjeto' );
    $obHdnObjeto->setValue ( $rsConvenio->getCampo( 'cod_objeto' ) );

    $obHdnTxtObejto = new Hidden;
    $obHdnTxtObejto->setId   ( 'txtObjeto' );
    $obHdnTxtObejto->setName ( 'txtObjeto' );
    $obHdnTxtObejto->setValue ( $rsConvenio->getCampo( 'descricao_objeto' ) );

    if ($boAlterar) {
        /* Data da Assinatura */
        list($ano,$mes,$dia) = explode ( '-' , $rsConvenio->getCampo( 'dt_assinatura' ));
        $obLblDtAssinatura = new Label();
        $obLblDtAssinatura->setRotulo  ( 'Data da Assinatura' );
        $obLblDtAssinatura->setValue   ( $dia.'/'.$mes.'/'.$ano );

        $obHdnDtAssinatura = new Hidden;
        $obHdnDtAssinatura->setId    ( 'dtAssinatura' );
        $obHdnDtAssinatura->setName  ( 'dtAssinatura' );
        $obHdnDtAssinatura->setValue ( $dia.'/'.$mes.'/'.$ano );

        /* Data do Final de Vigencia */
        list($ano,$mes,$dia) = explode ( '-' , $rsConvenio->getCampo( 'dt_vigencia' ));
        $obLblDtFinalVigencia = new Label();
        $obLblDtFinalVigencia->setRotulo  ( "Data do Final da Vigência" );
        $obLblDtFinalVigencia->setValue   ( $dia.'/'.$mes.'/'.$ano );

        $obHdnDtFinalVigencia   = new Hidden;
        $obHdnDtFinalVigencia->setId ( 'dtFinalVigencia' );
        $obHdnDtFinalVigencia->setName ( 'dtFinalVigencia' );
        $obHdnDtFinalVigencia->setValue ( $dia.'/'.$mes.'/'.$ano );

        /* Valor */
        $obLblValorConvenio = new Label();
        $obLblValorConvenio->setRotulo  ( 'Valor do Convênio' );
        $obLblValorConvenio->setValue   ( number_format( $rsConvenio->getCampo( 'valor' ) , 2 , ',' , '.' ) );

        $obHdnValorConvenio = new Hidden();
        $obHdnValorConvenio->setName     ( 'nuValorConvenio' );
        $obHdnValorConvenio->setId       ( 'nuValorConvenio' );
        $obHdnValorConvenio->setValue    ( $rsConvenio->getCampo('valor') );

        /* Data Início Execução*/
        $obDtInicioExecucao = new Label();
        $obDtInicioExecucao->setRotulo  ( 'Data de Início de Execução' );
        $obDtInicioExecucao->setValue   ( $rsConvenio->getCampo("inicio_execucao") );
        $obHdnDtInicioExecucao = new Hidden();
        $obHdnDtInicioExecucao->setName ( 'dtInicioExecucao' );
        $obHdnDtInicioExecucao->setId   ( 'dtInicioExecucao' );
        $obHdnDtInicioExecucao->setValue( $rsConvenio->getCampo("inicio_execucao") );
    }

} else {

    /* NUMERO DO CONVENIO*/
    $obIntNumConvenio = new Inteiro;
    $obIntNumConvenio->setName  ( "inNumConvenio" );
    $obIntNumConvenio->setId    ( "inNumConvenio" );
    $obIntNumConvenio->setRotulo( "Número do Convênio" );
    $obIntNumConvenio->setTitle ( "Informe o número do convênio" );
    $obIntNumConvenio->setNull  ( false );

    /* TIPO DE CONVENIO */
    require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoTipoConvenio.class.php");
    $obTLicitacaoTipoConvenio = new TLicitacaoTipoConvenio;
    $obTLicitacaoTipoConvenio->setDado('cod_uf_tipo_convenio', $cod_uf);
    $obTLicitacaoTipoConvenio->recuperaPorChave ( $rsTiposConvenio );

    $obCmbTiposConvenio = new Select;
    $obCmbTiposConvenio->setTitle ( "Selecione o tipo de convênio" );
    $obCmbTiposConvenio->setName ( "inCodTipoConvenio" );
    $obCmbTiposConvenio->setRotulo ( "Tipo de Convênio" );
    $obCmbTiposConvenio->setCampoId ( "cod_tipo_convenio" );
    $obCmbTiposConvenio->setCampoDesc ( "descricao" );
    $obCmbTiposConvenio->setNull ( false );
    $obCmbTiposConvenio->addOption ( "", "Selecione" );
    $obCmbTiposConvenio->preencheCombo ( $rsTiposConvenio );

    /* OBJETO */
    $obObjeto = new IPopUpEditObjeto($obForm);
    $obObjeto->setNull(false        );
    $obObjeto->setName  ("stObjeto");
    $obObjeto->setId  ("stObjeto");

    /* Data da Assinatura */
    $obDtAssinatura = new Data;
    $obDtAssinatura->setName    ( 'dtAssinatura' );
    $obDtAssinatura->setId      ( 'dtAssinatura' );
    $obDtAssinatura->setRotulo  ( 'Data da Assinatura' );
    $obDtAssinatura->setTitle   ( 'Informe a Data de Assinatura do Convênio.' );
    $obDtAssinatura->setValue   ( date( 'd/m/Y' ) );
    $obDtAssinatura->obEvento->setOnChange ( ' validaDatasAssinatura( this );');
    $obDtAssinatura->obEvento->setOnBlur   ( ' validaDatasAssinatura( this );');
    $obDtAssinatura->setNull    ( false );

    /* Data do Final de Vigencia */
    $obDtFinalVigencia = new Data;
    $obDtFinalVigencia->setName    ( 'dtFinalVigencia' );
    $obDtFinalVigencia->setId      ( 'dtFinalVigencia' );
    $obDtFinalVigencia->setRotulo  ( 'Data do Final da Vigência' );
    $obDtFinalVigencia->setTitle   ( 'Informe a Data do Final da Vigência.' );
    $obDtFinalVigencia->setValue   ( date( 'd/m/Y' ) );
    $obDtFinalVigencia->obEvento->setOnChange ( ' validaDatasAssinatura( this );');
    $obDtFinalVigencia->obEvento->setOnBlur   ( ' validaDatasAssinatura( this );');
    $obDtFinalVigencia->setNull    ( false );

    /* Valor */
    $obValorConvenio = new Numerico;
    $obValorConvenio->setName     ( 'nuValorConvenio' );
    $obValorConvenio->setId       ( 'nuValorConvenio' );
    $obValorConvenio->setMaxLength( 18 );
    $obValorConvenio->setSize     ( 18 );
    $obValorConvenio->setRotulo   ( 'Valor do Convênio' );
    $obValorConvenio->setTitle    ( 'Informe o Valor do Convênio' );
    $obValorConvenio->setNull     ( false );

    /* Data Início Execução*/
    $obDtInicioExecucao = new Data;
    $obDtInicioExecucao->setName    ( 'dtInicioExecucao' );
    $obDtInicioExecucao->setId      ( 'dtInicioExecucao' );
    $obDtInicioExecucao->setRotulo  ( 'Data de Início de Execução' );
    $obDtInicioExecucao->setTitle   ( 'Informe a data de início de execução do convênio.' );
    $obDtInicioExecucao->setValue   ( date('d/m/Y') );
    $obDtInicioExecucao->obEvento->setOnChange ( ' validaDatasAssinatura( this );');
    $obDtInicioExecucao->obEvento->setOnBlur   ( ' validaDatasAssinatura( this );');
    $obDtInicioExecucao->setNull    ( false );
}

/* Responsalve Juridico */
$obIpopUpCgm = new IPopUpCGMVinculado($obForm);
$obIpopUpCgm->setTabelaVinculo( 'sw_cgm_pessoa_fisica' );
$obIpopUpCgm->setCampoVinculo( 'numcgm' );
$obIpopUpCgm->setNomeVinculo( 'Responsavel' );
$obIpopUpCgm->setRotulo("Responsável Jurídico");
$obIpopUpCgm->setTitle("Selecione o Responsável Jurídico pelo convênio ");
$obIpopUpCgm->setNull ( false );
$obIpopUpCgm->setName   ( 'stResponsavelJuridico');
$obIpopUpCgm->setId     ( 'stResponsavelJuridico');
$obIpopUpCgm->obCampoCod->setName ( 'inCgmResponsavelJuridico' );
$obIpopUpCgm->obCampoCod->setId   ( 'inCgmResponsavelJuridico' );
if ($boAlterar) {
    $obIpopUpCgm->setValue ( $rsConvenio->getCampo( 'nom_cgm' ) );
    $obIpopUpCgm->obCampoCod->setValue ( $rsConvenio->getCampo( 'cgm_responsavel' ) );
} elseif ($boRescindir) {
    $obIpopUpCgm->setValue ( ( $stNomCgm != "" ? $stNomCgm : "" ) );
    $obIpopUpCgm->obCampoCod->setValue ( ( $inCgmResponsavelJuridico != "" ?  $inCgmResponsavelJuridico : "") );
}

if ($boRescindir == false) {

    /* Observação */
    $obTxtObs = new TextArea;
    $obTxtObs->setName   ( "stObservacao"                       );
    $obTxtObs->setId     ( "stObservacao"                       );
    $obTxtObs->setRotulo ( "Observações"                        );
    $obTxtObs->setTitle  ( "Digite observações pertinentes a este convênio" );
    $obTxtObs->setNull   ( true                                 );
    $obTxtObs->setRows   ( 3                                    );
    $obTxtObs->setCols   ( 64                                   );
    if ($boAlterar) {
        $obTxtObs->setValue ( $rsConvenio->getCampo( 'observacao' ) ) ;
    }
   
### Lei Autorizativa do Convenio ###
$obIPopUpLei = new IPopUpNorma();
$obIPopUpLei->obInnerNorma->setId('stFundamentacaoLegal');
$obIPopUpLei->obInnerNorma->obCampoCod->stId = 'inCodLei';
$obIPopUpLei->obInnerNorma->obCampoCod->setName( "inCodLei" );
$obIPopUpLei->obInnerNorma->setRotulo("Fundamentação Legal");
$obIPopUpLei->setExibeDataNorma(true);

} else {

    /* Data Rescisão*/
    $obDtRescisao = new Data;
    $obDtRescisao->setName    ( 'dtRescisao' );
    $obDtRescisao->setId      ( 'dtRescisao' );
    $obDtRescisao->setRotulo  ( 'Data de rescisão' );
    $obDtRescisao->setTitle   ( 'Informe a data de rescisão.' );
    $obDtRescisao->setValue   ( ( $dtRescisao != "" ? $dtRescisao : '' ) );
    $obDtRescisao->obEvento->setOnChange ( ' validaDataRescisao( this );');
    $obDtRescisao->obEvento->setOnBlur   ( ' validaDataRescisao( this );');
    $obDtRescisao->setNull    ( false );

    /* MULTA */
    $obVlMulta = new Moeda;
    $obVlMulta->setName   ( 'vlMulta' );
    $obVlMulta->setId     ( 'vlMulta' );
    $obVlMulta->setRotulo ( 'Multa' );
    $obVlMulta->setTitle  ( 'Informe o valor da multa.' );
    $obVlMulta->setValue  ( ( $vlMulta != "" ? number_format(str_replace(".", ",", $vlMulta), 2, ",", ".") : "") );
    $obVlMulta->setNull   ( false );

    /* INDENIZAÇÃO */
    $obVlIndenizacao = new Moeda;
    $obVlIndenizacao->setName   ( 'vlIndenizacao' );
    $obVlIndenizacao->setId     ( 'vlIndenizacao' );
    $obVlIndenizacao->setRotulo ( 'Indenização' );
    $obVlIndenizacao->setTitle  ( 'Informe o valor da indenização.' );
    $obVlIndenizacao->setValue  ( ( $vlIndenizacao != "" ? number_format(str_replace(".", ",", $vlIndenizacao), 2, ",", ".") : "") );
    $obVlIndenizacao->setNull   ( false );

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName   ( "stMotivo" );
    $obTxtMotivo->setId     ( "stMotivo" );
    $obTxtMotivo->setRotulo ( "Motivo"  );
    $obTxtMotivo->setTitle  ( "Informe o motivo da rescisão" );
    $obTxtMotivo->setNull   ( false );
    $obTxtMotivo->setValue  ( ( $stMotivo != "" ? $stMotivo : "") );
    $obTxtMotivo->setRows   ( 3 );

}

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setId    ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setValue ( ""              );

//Painel veiculos de publicidade
$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( '*Veículo de Publicação'         );
$obVeiculoPublicidade->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->setNull( true );
$obVeiculoPublicidade->obCampoCod->setNull( true );

$obDataPublicacao = new Data();
$obDataPublicacao->setId   ( "dtDataPublicacao" );
$obDataPublicacao->setName ( "dtDataPublicacao" );
$obDataPublicacao->setValue( date('d/m/Y') );
$obDataPublicacao->setRotulo( "Data de Publicação" );
$obDataPublicacao->setObrigatorioBarra( true );
$obDataPublicacao->setTitle( "Informe a data de publicação." );

$obNumeroPublicacao = new Inteiro();
$obNumeroPublicacao->setId   ( "inNumPublicacao" );
$obNumeroPublicacao->setName ( "inNumPublicacao" );
$obNumeroPublicacao->setValue( '' );
$obNumeroPublicacao->setRotulo( "Número Publicação" );
$obNumeroPublicacao->setObrigatorioBarra( false	);
$obNumeroPublicacao->setTitle( "Informe o Número da Publicação " );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

//Campo Observação da Publicação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId     ( "_stObservacao"                               );
$obTxtObservacao->setName   ( "_stObservacao"                               );
$obTxtObservacao->setValue  ( ""                                           );
$obTxtObservacao->setRotulo ( "Observação"                                 );
$obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacao->setObrigatorioBarra( false                               );
$obTxtObservacao->setRows   ( 2                                            );
$obTxtObservacao->setCols   ( 100                                          );
$obTxtObservacao->setMaxCaracteres( 80 );

if ($boRescindir) {

    $obDtPublicacaoRescisao = new Data;
    $obDtPublicacaoRescisao->setName    ( 'dtPublicacaoRescisao' );
    $obDtPublicacaoRescisao->setId      ( 'dtPublicacaoRescisao' );
    $obDtPublicacaoRescisao->setRotulo  ( '*Data de Publicação' );
    $obDtPublicacaoRescisao->setTitle   ( 'Informe a Data de Assinatura do Convênio.' );

    /* IMPLEMENTAR FUNÇÃO JAVASCRIPT 'validaDataRescisao' */
    $obDtPublicacaoRescisao->obEvento->setOnChange ( ' validaDataRescisao( this );');
    $obDtPublicacaoRescisao->obEvento->setOnBlur   ( ' validaDataRescisao( this );');

    $obTxtObsPublicacao = new TextBox;
    $obTxtObsPublicacao->setName      ( 'stObsPublicacao' );
    $obTxtObsPublicacao->setId        ( 'stObsPublicacao' );
    $obTxtObsPublicacao->setRotulo    ( 'Observação' );
    $obTxtObsPublicacao->setTitle     ( 'Informe a observação da publicação.' );
    $obTxtObsPublicacao->setNull      ( true );
    $obTxtObsPublicacao->setMaxLength ( 99 );
    $obTxtObsPublicacao->setSize      ( 100 );
}

if (!$boRescindir) {
    /* Dados dos Participantes */

    /* CGM */
    $obCgmParticipante =  new IPopUpCGMVinculado($obForm);
    $obCgmParticipante->setTabelaVinculo ( 'licitacao.participante_certificacao' );
    $obCgmParticipante->setCampoVinculo ( 'cgm_fornecedor' );
    $obCgmParticipante->setNomeVinculo ( 'Participante' );
    $obCgmParticipante->setRotulo("CGM");
    $obCgmParticipante->setTitle("Selecione o CGM do participante");
    $obCgmParticipante->setObrigatorioBarra ( true );
    $obCgmParticipante->setName   ( 'stNomCgmParticipante');
    $obCgmParticipante->setId     ( 'stNomCgmParticipante');
    $obCgmParticipante->obCampoCod->setName ( 'inCgmParticipante' );
    $obCgmParticipante->obCampoCod->setId   ( 'inCgmParticipante' );
    $obCgmParticipante->setNull ( true );

    /* Tipo de Participante */
    require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoTipoParticipante.class.php");
    $obTLicitacaoTipoParticipante = new TLicitacaoTipoParticipante;
    $obTLicitacaoTipoParticipante->recuperaTodos ( $rsTiposParticipante );

    $obCmbTiposParticipante = new Select;
    $obCmbTiposParticipante->setTitle ( "Selecione o tipo de participação" );
    $obCmbTiposParticipante->setName ( "inCodTipoParticipante" );
    $obCmbTiposParticipante->setId   ( "inCodTipoParticipante" );
    $obCmbTiposParticipante->setRotulo ( "Tipo de Participação" );
    $obCmbTiposParticipante->addOption ( "", "Selecione" );
    $obCmbTiposParticipante->setCampoId ( "cod_tipo_participante" );
    $obCmbTiposParticipante->setCampoDesc ( "descricao" );
    $obCmbTiposParticipante->preencheCombo ( $rsTiposParticipante );
    $obCmbTiposParticipante->setObrigatorioBarra ( true );

    /* Valor Participacao */
    $obValorParticipacao = new Numerico;
    $obValorParticipacao->setName   ( 'nuValorParticipacao' );
    $obValorParticipacao->setId     ( 'nuValorParticipacao' );
    $obValorParticipacao->setMaxLength ( 18 );
    $obValorParticipacao->setSize      ( 18 );
    $obValorParticipacao->setRotulo ( 'Valor de Participação' );
    $obValorParticipacao->setTitle  ( 'Informe o Valor de Participação' );
    $obValorParticipacao->obEvento->setOnChange ( "montaParametrosGET('atualizaParticipacao', 'nuValorConvenio,nuValorParticipacao,hdnPercentualParticipacao', true);" );
    $obValorParticipacao->setObrigatorioBarra ( true );

    /* Percentual de Participação */
    $obHdnPercentualParticipacao = new Hidden;
    $obHdnPercentualParticipacao->setId    ( 'hdnPercentualParticipacao');
    $obHdnPercentualParticipacao->setName  ( 'hdnPercentualParticipacao');
    $obHdnPercentualParticipacao->setValue ( '' );

    $obPercentualParticipacao = new Label;
    $obPercentualParticipacao->setId    ( 'nuPercentualParticipacao');
    $obPercentualParticipacao->setName  ( 'nuPercentualParticipacao');
    $obPercentualParticipacao->setRotulo( 'Percentual de Participação' );
    $obPercentualParticipacao->setValue ( '0,00 %' );

    $obTextFuncao = new TextBox;
    $obTextFuncao->setName      ( 'stFuncaoParticipante'            );
    $obTextFuncao->setId        ( 'stFuncaoParticipante'            );
    $obTextFuncao->setTitle     ( 'Digite a função do participante' );
    $obTextFuncao->setRotulo    ( 'Função'                          );
    $obTextFuncao->setNull      ( true                              );
    $obTextFuncao->setMaxLength ( 40                                );
    $obTextFuncao->setSize      ( 60 );
    $obTextFuncao->setObrigatorioBarra ( true );

    //botoes do participante
    $obBtnIncluirParticipante = new Button;
    $obBtnIncluirParticipante->setName              ( "btnIncluirParticipante" );
    $obBtnIncluirParticipante->setId                ( "btnIncluirParticipante" );
    $obBtnIncluirParticipante->setValue             ( "Incluir" );
    $obBtnIncluirParticipante->setTipo              ( "button" );
    $obBtnIncluirParticipante->obEvento->setOnClick ( "montaParametrosGET('incluirParticipante', '', true);" );
    $obBtnIncluirParticipante->setDisabled          ( false );

    $obBtnLimparParticipante = new Button;
    $obBtnLimparParticipante->setName               ( "btnLimparParticipante" );
    $obBtnLimparParticipante->setValue              ( "Limpar" );
    $obBtnLimparParticipante->setTipo               ( "button" );
    $obBtnLimparParticipante->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."', 'limpaParticipante');" );
    $obBtnLimparParticipante->setDisabled           ( false );

    $botoesParticipante = array ( $obBtnIncluirParticipante, $obBtnLimparParticipante );

    $obSpanParticipantes = new Span;
    $obSpanParticipantes->setId ( 'spnParticipantes' );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-03.05.14" );
$obFormulario->addHidden    ( $obHdnInExercicio );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnCodUf );
$obFormulario->addTitulo    ( "Dados do Convênio");
if ($boCampoLabel == true) {

    if ($boRescindir == true) {
        $obFormulario->addHidden    ( $obHdnExercicio );
        $obFormulario->addComponente( $obLblExercicio );
        $obFormulario->addHidden( $obHdnDtMaiorAditivo );
    }

    $obFormulario->addHidden 	( $obHdnNumConvenio );
    $obFormulario->addComponente( $obLblNumConvenio );
    $obFormulario->addComponente( $obLblTipoConvenio );
    $obFormulario->addHidden    ( $obHdnCodTipoConvenio );
    $obFormulario->addComponente( $obLblObjeto );
    $obFormulario->addHidden    ( $obHdnObjeto ) ;
    $obFormulario->addHidden    ( $obHdnTxtObejto );

    /*
        se existe dados para esse num_convenio e exercicio na tabela rescisao_convenio,
        adiciona o numero da rescisao na interface
    */
    if ($boAlterarRescisao) {
        $obFormulario->addHidden    ( $obHdnNumRescisao );
        $obFormulario->addComponente( $obLblNumRescisao );
        $obFormulario->addHidden    ( $obHdnExercicioRescisao );
    }

    if ($boAlterar) {
        $obFormulario->addComponente( $obLblDtAssinatura );
        $obFormulario->addHidden    ( $obHdnDtAssinatura );
        $obFormulario->addComponente( $obDtInicioExecucao );
        $obFormulario->addHidden    ( $obHdnDtInicioExecucao );
        $obFormulario->addComponente( $obLblDtFinalVigencia );
        $obFormulario->addHidden    ( $obHdnDtFinalVigencia );
        $obFormulario->addComponente( $obLblValorConvenio );
        $obFormulario->addHidden    ( $obHdnValorConvenio );
    }

} else {
    $obFormulario->addComponente( $obIntNumConvenio );
    $obFormulario->addComponente( $obCmbTiposConvenio );
    $obFormulario->addComponente( $obObjeto );
    $obFormulario->addComponente( $obDtAssinatura );
    $obFormulario->addComponente( $obDtInicioExecucao );
    $obFormulario->addComponente( $obDtFinalVigencia );
    $obFormulario->addComponente( $obValorConvenio   );
}

if (!$boRescindir) {
    $obIPopUpLei->geraFormulario($obFormulario);
    $obFormulario->addComponente( $obTxtObs );
    $obFormulario->addComponente( $obIpopUpCgm );
} else {
    $obFormulario->addComponente( $obDtRescisao );
    $obFormulario->addComponente( $obIpopUpCgm );
    $obFormulario->addComponente( $obVlMulta );
    $obFormulario->addComponente( $obVlIndenizacao );
    $obFormulario->addComponente( $obTxtMotivo );
}

$obFormulario->addTitulo        ( 'Veículo de Publicação' );
$obFormulario->addComponente    ( $obVeiculoPublicidade );
$obFormulario->addComponente    ( $obDataPublicacao );
$obFormulario->addComponente    ( $obNumeroPublicacao );
$obFormulario->addComponente    ( $obTxtObservacao );
$obFormulario->defineBarra      ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan          ( $obSpnListaVeiculo );
$obFormulario->addHidden        ( $obHdnCodVeiculo );

if (!$boRescindir) {
    $obFormulario->addTitulo    ( "Dados dos Participantes do Convênio");
    $obFormulario->addComponente( $obCgmParticipante );
    $obFormulario->addComponente( $obCmbTiposParticipante );
    $obFormulario->addComponente( $obValorParticipacao );
    $obFormulario->addHidden    ( $obHdnPercentualParticipacao );
    $obFormulario->addComponente( $obPercentualParticipacao );
    $obFormulario->addComponente( $obTextFuncao             );
    $obFormulario->defineBarra  ( $botoesParticipante, 'left', '' );
    $obFormulario->addSpan      ( $obSpanParticipantes   );
}

$obFormulario->Ok();
$obFormulario->show();

if ($stAcao == 'alterar') {
    echo "<script type=\"text/javascript\">             \r\n";
    echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumConvenio=".$_REQUEST['inNumConvenio']. "', 'montaListas');     \r\n";
    echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodLei=".$rsConvenio->getCampo("cod_norma_autorizativa"). "', 'montaBuscaNorma');     \r\n";
    echo "</script>                                                             \r\n";
} if ($stAcao == 'rescindir') {
    echo "<script type=\"text/javascript\">             \r\n";
    echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumConvenio=".$_REQUEST['inNumConvenio']."&inExercicio=".$_REQUEST['inExercicio']."', 'montaListasPublicidadeRescisao');     \r\n";
    echo "</script>                                                             \r\n";
}
