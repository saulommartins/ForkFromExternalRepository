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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOUnidadeResponsavel.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOTipoResponsavel.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOProvimentoContabil.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOProvimentoJuridico.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOContadorTerceirizado.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOJuridicoTerceirizado.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {

    case 'buscaConfiguracao':
        $obForm = new Form;
        $obForm->setAction                  ( $pgProc );
        $obForm->setTarget                  ( "oculto" );

        $obTUF = new TUF();
        $stFiltro = " WHERE cod_pais = 1 ";
        $stOrder = " sigla_uf ASC ";
        $obTUF->recuperaTodos( $rsUF, $stFiltro, $stOrder );

        $obTTCMGOUnidadeResponsavel = new TTCMGOTipoResponsavel();
        $obTTCMGOUnidadeResponsavel->recuperaTodos( $rsTipoResponsavel );

        $obTTCMGOProvimentoContabil = new TTCMGOProvimentoContabil();
        $obTTCMGOProvimentoContabil->recuperaTodos( $rsProvimentoContabil, '', 'cod_provimento' );

        $obTTCMGOProvimentoJuridico = new TTCMGOProvimentoJuridico();
        $obTTCMGOProvimentoJuridico->recuperaTodos( $rsProvimentoJuridico, '', 'cod_provimento' );

        /* GESTOR */

        $obCGMGestor = new IPopUpCGMVinculado( $obForm );
        $obCGMGestor->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica' );
        $obCGMGestor->setCampoVinculo     ( 'numcgm'      );
        $obCGMGestor->setNomeVinculo      ( 'Gestor'      );
        $obCGMGestor->setRotulo           ( 'Gestor'      );
        $obCGMGestor->setName             ( 'stNomGestor' );
        $obCGMGestor->setId               ( 'stNomGestor' );
        $obCGMGestor->obCampoCod->setName ( 'inCGMGestor' );
        $obCGMGestor->obCampoCod->setId   ( 'inCGMGestor' );

        $obDtInicioGestor = new Data();
        $obDtInicioGestor->setName( 'dtInicioGestor' );
        $obDtInicioGestor->setId( 'dtInicioGestor' );
        $obDtInicioGestor->setRotulo( 'Data de Início' );
        $obDtInicioGestor->setNull( false );

        $obDtFimGestor = new Data();
        $obDtFimGestor->setName( 'dtFimGestor' );
        $obDtFimGestor->setId( 'dtFimGestor' );
        $obDtFimGestor->setRotulo( 'Data de Término' );

        $obCmbTipoResponsavelGestor = new Select;
        $obCmbTipoResponsavelGestor->setRotulo ('Tipo responsável');
        $obCmbTipoResponsavelGestor->setName('inTipoResponsavelGestor');
        $obCmbTipoResponsavelGestor->setId('inTipoResponsavelGestor');
        $obCmbTipoResponsavelGestor->setCampoId( "[cod_tipo]" );
        $obCmbTipoResponsavelGestor->setCampoDesc( "[descricao]" );
        $obCmbTipoResponsavelGestor->addOption( "", "Selecione" );
        $obCmbTipoResponsavelGestor->preencheCombo( $rsTipoResponsavel );
        $obCmbTipoResponsavelGestor->setNull( false );

        $obTxtCargoGestor = new TextBox();
        $obTxtCargoGestor->setRotulo( 'Cargo' );
        $obTxtCargoGestor->setName( 'stCargoGestor' );
        $obTxtCargoGestor->setId( 'stCargoGestor' );
        $obTxtCargoGestor->setSize( 50 );
        $obTxtCargoGestor->setMaxLength( 50 );

        /* CONTADOR */

        $obCGMContador = new IPopUpCGMVinculado( $obForm );
        $obCGMContador->setTabelaVinculo( 'sw_cgm'   );
        $obCGMContador->setCampoVinculo( 'numcgm' );
        $obCGMContador->setNomeVinculo( 'Contador' );
        $obCGMContador->setRotulo( 'Contador' );
        $obCGMContador->setName( 'stNomContador' );
        $obCGMContador->setId( 'stNomContador' );
        $obCGMContador->obCampoCod->setName ( 'inCGMContador' );
        $obCGMContador->obCampoCod->setId( 'inCGMContador' );
        $obCGMContador->setNull( false );

        $obDtInicioContador = new Data();
        $obDtInicioContador->setName( 'dtInicioContador' );
        $obDtInicioContador->setId( 'dtInicioContador' );
        $obDtInicioContador->setRotulo( 'Data de Início' );
        $obDtInicioContador->setNull( false );

        $obDtFimContador = new Data();
        $obDtFimContador->setName( 'dtFimContador' );
        $obDtFimContador->setId( 'dtFimContador' );
        $obDtFimContador->setRotulo( 'Data de Término' );

        $obTxtCRCContador = new TextBox();
        $obTxtCRCContador->setRotulo( 'CRC' );
        $obTxtCRCContador->setTitle( 'Número do CRC do Contador' );
        $obTxtCRCContador->setName( 'stCRCContador' );
        $obTxtCRCContador->setId( 'stCRCContador' );
        $obTxtCRCContador->setSize( 11 );
        $obTxtCRCContador->setMaxLength( 11 );

        $obCmbUFCRC = new Select();
        $obCmbUFCRC->setName( "inSiglaUFContador" );
        $obCmbUFCRC->setId( "inSiglaUFCRCContador" );
        $obCmbUFCRC->setRotulo( "UF CRC" );
        $obCmbUFCRC->setValue( $inSiglaUFContador );
        $obCmbUFCRC->setTitle( "Estado do registro do CRC" );
        $obCmbUFCRC->setCampoId( "[cod_uf]" );
        $obCmbUFCRC->setCampoDesc( "[sigla_uf]" );
        $obCmbUFCRC->addOption( "", "Selecione" );
        $obCmbUFCRC->preencheCombo( $rsUF );

        $obCmbProvimentoContador = new Select;
        $obCmbProvimentoContador->setRotulo ('Provimento');
        $obCmbProvimentoContador->setName('inProvimentoContabil');
        $obCmbProvimentoContador->setId('inProvimentoContabil');
        $obCmbProvimentoContador->setTitle('Indicar a situação do contador');
        $obCmbProvimentoContador->setCampoId( "[cod_provimento]" );
        $obCmbProvimentoContador->setCampoDesc( "[descricao]" );
        $obCmbProvimentoContador->addOption( "", "Selecione" );
        $obCmbProvimentoContador->preencheCombo( $rsProvimentoContabil );
        $obCmbProvimentoContador->obEvento->setOnChange('buscaCGMTerceirizadaContador(this);');

        $obSpanCGMTerceirizadaContador = new Span;
        $obSpanCGMTerceirizadaContador->setId('spnCGMTerceirizadaContador');

        /* RESPONSÁVEL DO CONTROLE INTERNO */

        $obCGMControleInterno = new IPopUpCGMVinculado( $obForm );
        $obCGMControleInterno->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica'   );
        $obCGMControleInterno->setCampoVinculo     ( 'numcgm'      );
        $obCGMControleInterno->setNomeVinculo      ( 'Controle Interno'      );
        $obCGMControleInterno->setRotulo           ( 'Controle Interno'      );
        $obCGMControleInterno->setName             ( 'stNomControleInterno' );
        $obCGMControleInterno->setId               ( 'stNomControleInterno' );
        $obCGMControleInterno->obCampoCod->setName ( 'inCGMControleInterno' );
        $obCGMControleInterno->obCampoCod->setId   ( 'inCGMControleInterno' );
        $obCGMControleInterno->setNull( false );

        $obDtInicioControleInterno = new Data();
        $obDtInicioControleInterno->setName( 'dtInicioControleInterno' );
        $obDtInicioControleInterno->setId( 'dtInicioControleInterno' );
        $obDtInicioControleInterno->setRotulo( 'Data de Início' );
        $obDtInicioControleInterno->setNull( false );

        $obDtFimControleInterno = new Data();
        $obDtFimControleInterno->setName( 'dtFimControleInterno' );
        $obDtFimControleInterno->setId( 'dtFimControleInterno' );
        $obDtFimControleInterno->setRotulo( 'Data de Término' );

        /* JURÍDICO */

        $obCGMJuridico = new IPopUpCGMVinculado( $obForm );
        $obCGMJuridico->setTabelaVinculo( 'sw_cgm'   );
        $obCGMJuridico->setCampoVinculo( 'numcgm' );
        $obCGMJuridico->setNomeVinculo( 'Juridico' );
        $obCGMJuridico->setRotulo( 'Jurídico' );
        $obCGMJuridico->setName( 'stNomJuridico' );
        $obCGMJuridico->setId( 'stNomJuridico' );
        $obCGMJuridico->obCampoCod->setName ( 'inCGMJuridico' );
        $obCGMJuridico->obCampoCod->setId( 'inCGMJuridico' );
        $obCGMJuridico->setNull( false );

        $obDtInicioJuridico = new Data();
        $obDtInicioJuridico->setName( 'dtInicioJuridico' );
        $obDtInicioJuridico->setId( 'dtInicioJuridico' );
        $obDtInicioJuridico->setRotulo( 'Data de Início' );
        $obDtInicioJuridico->setNull( false );

        $obDtFimJuridico = new Data();
        $obDtFimJuridico->setName( 'dtFimJuridico' );
        $obDtFimJuridico->setId( 'dtFimJuridico' );
        $obDtFimJuridico->setRotulo( 'Data de Término' );

        $obTxtOABJuridico = new TextBox();
        $obTxtOABJuridico->setRotulo( 'OAB' );
        $obTxtOABJuridico->setTitle( 'Número do OAB do responsável do Setor Jurídico' );
        $obTxtOABJuridico->setName( 'stOABJuridico' );
        $obTxtOABJuridico->setId( 'stOABJuridico' );
        $obTxtOABJuridico->setSize( 8 );
        $obTxtOABJuridico->setMaxLength( 8 );

        $obCmbUFOAB = new Select();
        $obCmbUFOAB->setName( "inSiglaUFJuridico" );
        $obCmbUFOAB->setId( "inSiglaUFJuridico" );
        $obCmbUFOAB->setRotulo( "UF OAB" );
        $obCmbUFOAB->setValue( $stSiglaUF );
        $obCmbUFOAB->setTitle( "Estado do registro do responsável do Setor Jurídico." );
        $obCmbUFOAB->setCampoId( "[cod_uf]" );
        $obCmbUFOAB->setCampoDesc( "[sigla_uf]" );
        $obCmbUFOAB->addOption( "", "Selecione" );
        $obCmbUFOAB->preencheCombo( $rsUF );

        $obCmbProvimentoJuridico = new Select;
        $obCmbProvimentoJuridico->setRotulo ('Provimento');
        $obCmbProvimentoJuridico->setName('inProvimentoJuridico');
        $obCmbProvimentoJuridico->setId('inProvimentoJuridico');
        $obCmbProvimentoJuridico->setTitle('Indicar a situação de investidura do responsável do setor Jurídico do órgão');
        $obCmbProvimentoJuridico->setCampoId( "[cod_provimento]" );
        $obCmbProvimentoJuridico->setCampoDesc( "[descricao]" );
        $obCmbProvimentoJuridico->addOption( "", "Selecione" );
        $obCmbProvimentoJuridico->preencheCombo( $rsProvimentoJuridico );
        $obCmbProvimentoJuridico->obEvento->setOnChange('buscaCGMTerceirizadaJuridico(this);');

        $obSpanCGMTerceirizadaJuridico = new Span;
        $obSpanCGMTerceirizadaJuridico->setId('spnCGMTerceirizadaJuridico');

        //SE EXISTIR JÁ EXISTR UM REGISTRO, SETA CAMPOS
        $obTTCMGOUnidadeResponsavel = new TTCMGOUnidadeResponsavel();
        $obTTCMGOUnidadeResponsavel->setDado('exercicio', Sessao::getExercicio());
        $obTTCMGOUnidadeResponsavel->setDado('num_orgao', $_REQUEST['inOrgao']);
        $obTTCMGOUnidadeResponsavel->setDado('num_unidade', $_REQUEST['inUnidade']);
        $obTTCMGOUnidadeResponsavel->recuperaPorUnidade( $rsUnidade );

        if ($rsUnidade->getNumLinhas() > 0) {
            $obCGMGestor->obCampoCod->setValue($rsUnidade->getCampo('cgm_gestor'));
            $obDtInicioGestor->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('gestor_dt_inicio')));
            $obDtFimGestor->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('gestor_dt_fim')));
            $obCmbTipoResponsavelGestor->setValue($rsUnidade->getCampo('tipo_responsavel'));
            $obTxtCargoGestor->setValue($rsUnidade->getCampo('gestor_cargo'));

            $obCGMContador->obCampoCod->setValue($rsUnidade->getCampo('cgm_contador'));
            $obDtInicioContador->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('contador_dt_inicio')));
            $obDtFimContador->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('contador_dt_fim')));
            $obTxtCRCContador->setValue($rsUnidade->getCampo('contador_crc'));
            $obCmbUFCRC->setValue($rsUnidade->getCampo('uf_crc'));
            $obCmbProvimentoContador->setValue($rsUnidade->getCampo('cod_provimento_contabil'));

            $obCGMControleInterno->obCampoCod->setValue($rsUnidade->getCampo('cgm_controle_interno'));
            $obDtInicioControleInterno->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('controle_interno_dt_inicio')));
            $obDtFimControleInterno->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('controle_interno_dt_fim')));

            $obCGMJuridico->obCampoCod->setValue($rsUnidade->getCampo('cgm_juridico'));
            $obDtInicioJuridico->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('juridico_dt_inicio')));
            $obDtFimJuridico->setValue(SistemaLegado::dataToBr($rsUnidade->getCampo('juridico_dt_fim')));
            $obTxtOABJuridico->setValue($rsUnidade->getCampo('juridico_oab'));
            $obCmbUFOAB->setValue($rsUnidade->getCampo('uf_oab'));
            $obCmbProvimentoJuridico->setValue($rsUnidade->getCampo('cod_provimento_juridico'));

            if ($rsUnidade->getCampo('cod_provimento_contabil') == 4) {
                $obTTCMGOContadorTerceirizado = new TTCMGOContadorTerceirizado();
                $obTTCMGOContadorTerceirizado->setDado('exercicio'  , $rsUnidade->getCampo('exercicio'));
                $obTTCMGOContadorTerceirizado->setDado('num_orgao'  , $rsUnidade->getCampo('num_orgao'));
                $obTTCMGOContadorTerceirizado->setDado('num_unidade', $rsUnidade->getCampo('num_unidade'));
                $obTTCMGOContadorTerceirizado->setDado('timestamp'  , $rsUnidade->getCampo('timestamp'));
                $obTTCMGOContadorTerceirizado->recuperaPorUnidade( $rsContador );
            }

            if ($rsUnidade->getCampo('cod_provimento_juridico') == 4) {
                $obTTCMGOJuridicoTerceirizado = new TTCMGOJuridicoTerceirizado();
                $obTTCMGOJuridicoTerceirizado->setDado('exercicio'  , $rsUnidade->getCampo('exercicio'));
                $obTTCMGOJuridicoTerceirizado->setDado('num_orgao'  , $rsUnidade->getCampo('num_orgao'));
                $obTTCMGOJuridicoTerceirizado->setDado('num_unidade', $rsUnidade->getCampo('num_unidade'));
                $obTTCMGOJuridicoTerceirizado->setDado('timestamp'  , $rsUnidade->getCampo('timestamp'));
                $obTTCMGOJuridicoTerceirizado->recuperaPorUnidade( $rsJuridico );
            }
        }

        //FORMULÁRIO
        $obFormulario = new Formulario;
        $obFormulario->addTitulo('Dados do Gestor/Ordenador de Despesa por Unidade Orçamentária');
        $obFormulario->addComponente( $obCGMGestor );
        $obFormulario->addComponente( $obDtInicioGestor );
        $obFormulario->addComponente( $obDtFimGestor );
        $obFormulario->addComponente( $obCmbTipoResponsavelGestor );
        $obFormulario->addComponente( $obTxtCargoGestor );

        $obFormulario->addTitulo('Dados do Contador Responsável pela Unidade Orçamentária');
        $obFormulario->addComponente( $obCGMContador );
        $obFormulario->addComponente( $obDtInicioContador );
        $obFormulario->addComponente( $obDtFimContador );
        $obFormulario->addComponente( $obTxtCRCContador );
        $obFormulario->addComponente( $obCmbUFCRC );
        $obFormulario->addComponente( $obCmbProvimentoContador );
        $obFormulario->addSpan( $obSpanCGMTerceirizadaContador );

        $obFormulario->addTitulo('Dados do Responsável do Controle Interno pela Unidade Orçamentária');
        $obFormulario->addComponente( $obCGMControleInterno );
        $obFormulario->addComponente( $obDtInicioControleInterno );
        $obFormulario->addComponente( $obDtFimControleInterno );

        $obFormulario->addTitulo('Dados do Responsável do Setor Jurídico pela Unidade Orçamentária');
        $obFormulario->addComponente( $obCGMJuridico );
        $obFormulario->addComponente( $obDtInicioJuridico );
        $obFormulario->addComponente( $obDtFimJuridico );
        $obFormulario->addComponente( $obTxtOABJuridico );
        $obFormulario->addComponente( $obCmbUFOAB );
        $obFormulario->addComponente( $obCmbProvimentoJuridico );
        $obFormulario->addSpan( $obSpanCGMTerceirizadaJuridico );

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnConfiguracao').innerHTML = '".$stHtml."';</script>";
        echo $js;

        $js= "<script>";
        $js.= "window.parent.frames['telaPrincipal'].document.getElementById('stNomGestor').innerHTML          = '".$rsUnidade->getCampo('nom_gestor')."';";
        $js.= "window.parent.frames['telaPrincipal'].document.getElementById('stNomContador').innerHTML        = '".$rsUnidade->getCampo('nom_contador')."';";
        $js.= "window.parent.frames['telaPrincipal'].document.getElementById('stNomJuridico').innerHTML        = '".$rsUnidade->getCampo('nom_juridico')."';";
        $js.= "window.parent.frames['telaPrincipal'].document.getElementById('stNomGestor').innerHTML          = '".$rsUnidade->getCampo('nom_gestor')."';";
        $js.= "window.parent.frames['telaPrincipal'].document.getElementById('stNomControleInterno').innerHTML = '".$rsUnidade->getCampo('nom_controle_interno')."';";
        $js.= "</script>";
        echo $js;

        if ($rsUnidade->getCampo('cod_provimento_contabil') == 4) {
            montaTerceirizadaContador($rsContador);
        }

        if ($rsUnidade->getCampo('cod_provimento_juridico') == 4) {
            montaTerceirizadaJuridico($rsJuridico);
        }

    break;

    case 'buscaCGMTerceirizadaContador':
        montaTerceirizadaContador();
    break;

    case 'buscaCGMTerceirizadaJuridico':
        montaTerceirizadaJuridico();
    break;

    case 'buscaUnidade':
        include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

        $obForm = new Form;
        $obForm->setAction( $pgProc );
        $obForm->setTarget( "oculto" );

        $obTOrcamentoUnidade = new TOrcamentoUnidade();
        $obTOrcamentoUnidade->setDado('exercicio', Sessao::getExercicio());
        $obTOrcamentoUnidade->setDado('num_orgao', $_REQUEST['inOrgao']);
        $obTOrcamentoUnidade->recuperaPorOrgao( $rsUnidade );

        $obCmbUnidade = new Select();
        $obCmbUnidade->setRotulo( 'Unidade' );
        $obCmbUnidade->setTitle( 'Selecione a Unidade' );
        $obCmbUnidade->setName( 'inUnidade' );
        $obCmbUnidade->setId( 'inUnidade' );
        $obCmbUnidade->addOption( '', 'Selecione' );
        $obCmbUnidade->setCampoId( 'num_unidade' );
        $obCmbUnidade->setCampoDesc( 'nom_unidade' );
        $obCmbUnidade->setStyle('width: 520');
        $obCmbUnidade->obEvento->setOnChange('buscaConfiguracao(this);');
        $obCmbUnidade->preencheCombo( $rsUnidade );
        $obCmbUnidade->setNull( false );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obCmbUnidade );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnUnidade').innerHTML = '".$stHtml."';</script>";
        $js.= "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnConfiguracao').innerHTML = '';</script>";
        echo $js;
    break;
}

function montaTerceirizadaContador($rs="")
{
    $obForm = new Form;

    $obCGMTerceirizadaContador = new IPopUpCGMVinculado( $obForm );
    $obCGMTerceirizadaContador->setTabelaVinculo( 'sw_cgm_pessoa_juridica'   );
    $obCGMTerceirizadaContador->setCampoVinculo( 'numcgm' );
    $obCGMTerceirizadaContador->setNomeVinculo( 'CGMTerceirizadaContador' );
    $obCGMTerceirizadaContador->setRotulo( 'CGM Terceirizada' );
    $obCGMTerceirizadaContador->setName( 'stNomCGMTerceirizadaContador' );
    $obCGMTerceirizadaContador->setId( 'stNomCGMTerceirizadaContador' );
    $obCGMTerceirizadaContador->obCampoCod->setName ( 'inCGMTerceirizadaContador' );
    $obCGMTerceirizadaContador->obCampoCod->setId( 'inCGMTerceirizadaContador' );

    if ($rs != "") {
        $obCGMTerceirizadaContador->obCampoCod->setValue($rs->getCampo('numcgm'));
    }

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCGMTerceirizadaContador );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnCGMTerceirizadaContador').innerHTML = '".$stHtml."';</script>";
    echo $js;

    if ($rs != "") {
        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('stNomCGMTerceirizadaContador').innerHTML   = '".$rs->getCampo('nom_cgm')."';</script>";
        echo $js;
    }
}

function montaTerceirizadaJuridico($rs="")
{
    $obForm = new Form;

    $obCGMTerceirizadaJuridico = new IPopUpCGMVinculado( $obForm );
    $obCGMTerceirizadaJuridico->setTabelaVinculo( 'sw_cgm_pessoa_juridica'   );
    $obCGMTerceirizadaJuridico->setCampoVinculo( 'numcgm' );
    $obCGMTerceirizadaJuridico->setNomeVinculo( 'CGMTerceirizadaJuridico' );
    $obCGMTerceirizadaJuridico->setRotulo( 'CGM Terceirizada' );
    $obCGMTerceirizadaJuridico->setName( 'stNomCGMTerceirizadaJuridico' );
    $obCGMTerceirizadaJuridico->setId( 'stNomCGMTerceirizadaJuridico' );
    $obCGMTerceirizadaJuridico->obCampoCod->setName ( 'inCGMTerceirizadaJuridico' );
    $obCGMTerceirizadaJuridico->obCampoCod->setId( 'inCGMTerceirizadaJuridico' );

    if ($rs != "") {
        $obCGMTerceirizadaJuridico->obCampoCod->setValue($rs->getCampo('numcgm'));
    }

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCGMTerceirizadaJuridico );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnCGMTerceirizadaJuridico').innerHTML = '".$stHtml."';</script>";
    echo $js;

    if ($rs != "") {
        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('stNomCGMTerceirizadaJuridico').innerHTML   = '".$rs->getCampo('nom_cgm')."';</script>";
        echo $js;
    }
}
