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
    * Página de
    * Data de criação : 24/10/2005

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephanou

    Caso de uso: uc-03.01.21

    $Id: OCListaPatrimonial.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioGrupo.class.php';
include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioNatureza.class.php';
include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioEspecie.class.php';
include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioBem.class.php';
include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioRelatorioListaPatrimonial.class.php';

$obPatrimonioNatureza = new RPatrimonioNatureza;
$obPatrimonioGrupo    = new RPatrimonioGrupo;
$obPatrimonioEspecie  = new RPatrimonioEspecie;

switch ($_REQUEST['stCtrl']) {
    case "MontaGrupo":
        $stGrupo  = "inCodGrupo";
        $stJs .= "limpaSelect(f.$stGrupo,0); \n";
        $stJs .= "f.$stGrupo.options[0] = new Option('Selecione','', 'selected');\n";

        $stCombo  = "inCodEspecie";
        $stJs  .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodNatureza"]) {
            $obPatrimonioGrupo->obRPatrimonioNatureza->setCodNatureza($_REQUEST["inCodNatureza"]);

            $obPatrimonioGrupo->listar( $rsGrupo, $stFiltro,"", $boTransacao );
            $rsGrupo->ordena('nom_grupo',ASC,SORT_STRING);
            $inCount = 0;

            while (!$rsGrupo->eof()) {
                $inCount++;
                $inId   = $rsGrupo->getCampo("cod_grupo");
                $stDesc = $rsGrupo->getCampo("nom_grupo");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stGrupo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsGrupo->proximo();
            }
        }

        $stJs .= $js;
        SistemaLegado::executaFrameOculto( $stJs );

    break;

    case "MontaEspecie":

        $stCombo  = "inCodEspecie";
        $stJs  = "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodGrupo"]) {
             $obPatrimonioEspecie->obRPatrimonioGrupo->obRPatrimonioNatureza->setCodNatureza($_REQUEST["inCodNatureza"]);
             $obPatrimonioEspecie->obRPatrimonioGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
             $obPatrimonioEspecie->setCodEspecie($_REQUEST["inCodEspecie"]);
             $obPatrimonioEspecie->listar( $rsCombo, $stFiltro,"", $boTransacao );
             $rsCombo->ordena('nom_especie',ASC,SORT_STRING);
            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("cod_especie");
                $stDesc = $rsCombo->getCampo("nom_especie");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }

        $stJs .= $js;
        SistemaLegado::executaFrameOculto( $stJs );

    break;
    case 'montaAtributos' :

    if ($_REQUEST['stCodClassificacao']) {
        $arClassificacao = explode( '.',$_REQUEST['stCodClassificacao'] );
        list( $_REQUEST['inCodNatureza'], $_REQUEST['inCodGrupo'], $_REQUEST['inCodEspecie'] ) = $arClassificacao;
    }

    if ($_REQUEST['inCodEspecie'] OR $_REQUEST['stCodClassificacao']) {

        $obRCadastroDinamico = new RCadastroDinamico();
        $obRCadastroDinamico->setCodCadastro( 1 );
        $obRCadastroDinamico->obRModulo->setCodModulo( 6 );

        if ($_REQUEST['inCodBem']) {
            $obRCadastroDinamico->setChavePersistenteValores( array( 'cod_bem' => $_REQUEST['inCodBem'], 'cod_especie' => $_REQUEST['inCodEspecie'], 'cod_grupo' => $_REQUEST['inCodGrupo'] ,'cod_natureza' => $_REQUEST['inCodNatureza'] ) );
            $obRCadastroDinamico->setPersistenteAtributos( new TPatrimonioEspecieAtributo );
            $obRCadastroDinamico->setPersistenteValores( new TPatrimonioBemAtributoEspecie );
            $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosAux );
        } else {
            $obRCadastroDinamico->setChavePersistenteValores( array( 'cod_especie' => $_REQUEST['inCodEspecie'], 'cod_grupo' => $_REQUEST['inCodGrupo'] ,'cod_natureza' => $_REQUEST['inCodNatureza'] ) );
            $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosAux );
        }

        //recupera os registros da table patrimonio.especie_atributo que estão ativos
        $obTPatrimonioEspecieAtributo = new TPatrimonioEspecieAtributo();
        $obTPatrimonioEspecieAtributo->setDado( 'cod_modulo', 6 );
        $obTPatrimonioEspecieAtributo->setDado( 'cod_cadastro', 1 );
        $obTPatrimonioEspecieAtributo->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
        $obTPatrimonioEspecieAtributo->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
        $obTPatrimonioEspecieAtributo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
        $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'true' );
        $obTPatrimonioEspecieAtributo->recuperaEspecieAtributo( $rsAtributosAtivos );

        $arAtivas = array();

        while ( !$rsAtributosAtivos->eof() ) {
            $arAtivas[] = $rsAtributosAtivos->getCampo('cod_atributo');
            $rsAtributosAtivos->proximo();
        }

        $rsAtributos = new RecordSet();

        for ( $i = 0; $i < $rsAtributosAux->getNumLinhas(); $i++ ) {
            if ( in_array( $rsAtributosAux->arElementos[$i]['cod_atributo'], $arAtivas ) ) {
                $rsAtributos->add( $rsAtributosAux->arElementos[$i] );
            }
        }

        //monta os atributos dinamicos
        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );
        $obMontaAtributos->recuperaValores();

        if ( $rsAtributos->getNumLinhas() > 0 ) {
            $obFormulario = new Formulario();
            $obMontaAtributos->geraFormulario( $obFormulario );
            $obFormulario->montaInnerHTML();

            //passa pela sessão o recordset de atributos para fazer a verificação no PR
                        Sessao::write('rsAtributosDinamicos',$rsAtributosDinamicos);

                        $stJs.= "document.getElementById('spnAtributos').innerHTML = '".$obFormulario->getHTML()."';";
            //$stJs.= "$('spnAtributos').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            //reseta o transf
                        Sessao::remove('rsAtributosDinamicos');
                        $stJs.= "document.getElementById('spnAtributos').innerHTML = '';";
            //$stJs.= "$('spnAtributos').innerHTML = '';";
        }
    } else {
        //$stJs.= "$('spnAtributos').innerHTML = '';";
    }
    break;

      case 'montaListaAtributos':
        unset($obAtributo);
    //padrao para identificacao do atributo "cod_modulo,cod_cadastro,cod_atributo"
    $arAtributo = explode(',', $_REQUEST['stAtributo']);

    $rsAtributo = new RecordSet();
    $stFiltro = " AND ad.cod_modulo = ".$arAtributo[0]."
              AND ad.cod_cadastro = ".$arAtributo[1]."
              AND ad.cod_atributo = ".$arAtributo[2]."
              ";

        require_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
    require_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
    $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();
    $obTAdministracaoAtributoDinamico->recuperaRelacionamento($rsAtributo, $stFiltro);

    while (!$rsAtributo->eof()) {
        switch ($rsAtributo->getCampo('cod_tipo')) {
            case 1: //númerico
                $obAtributo = new Inteiro();
                $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );//pensar em como setar o name e o id
                $obAtributo->setNull( true );
            break;
            case 2: //texto
                $obAtributo = new TextBox();
                $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                $obAtributo->setNull( true );
                $obAtributo->setMaxLength( 100 );
                $obAtributo->setSize( 70 );
            break;
            case 3: //Lista
                $stFiltro = " WHERE bem_atributo_especie.cod_modulo = ".$arAtributo[0]."
                        AND bem_atributo_especie.cod_cadastro = ".$arAtributo[1]."
                        AND bem_atributo_especie.cod_atributo = ".$arAtributo[2];
                require_once(CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php");
                $obTPatrimonioBemAtributoEspecie = new TPatrimonioBemAtributoEspecie();
                $obTPatrimonioBemAtributoEspecie->recuperaAtributosValores($rsAtributosValoresLista, $stFiltro);

                $obAtributo = new Select;
                $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                $obAtributo->setValue( "" );
                $obAtributo->setStyle( "width: 200px" );
                $obAtributo->addOption( "", "Selecione" );
                while (!$rsAtributosValoresLista->eof()) {
                    $obAtributo->addOption( $rsAtributosValoresLista->getCampo('cod_valor'), $rsAtributosValoresLista->getCampo('valor_padrao') );
                    $rsAtributosValoresLista->proximo();
                }
                $obAtributo->setNull( true );
            break;
            case 4: //Lista múltipla
                //verificar valores do atributo na tabela
//				$obAtributo = new SelectMultiplo;
//				$obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
//				$obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
//				$obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
//				$obAtributo->setId( "" );
//				$obAtributo->setNull( true );
//
//				// lista de atributos disponiveis
//				$obAtributo->SetNomeLista1 ('inCodAlmoxarifadoDisponivel');
//				$obAtributo->setCampoId1   ( 'codigo' );
//				$obAtributo->setCampoDesc1 ( '[codigo]-[nom_a]' );
//				$obAtributo->SetRecord1    ( $rsAlmoxarifados );
//				$rsRecordset = new RecordSet;
//				// lista de atributos selecionados
//				$obAtributo->SetNomeLista2 ('inCodAlmoxarifado');
//				$obAtributo->setCampoId2   ('codigo');
//				$obAtributo->setCampoDesc2 ('[codigo]-[nom_a]');
//				$obAtributo->SetRecord2    ( $rsRecordset );
            break;
            case 5: //Data
                //instancia um componente periodicidade
                $obAtributo = new Periodicidade();
                $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                $obAtributo->setIdComponente( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                $obAtributo->setName( '' );
                $obAtributo->setNull( true );
                $obAtributo->setExercicio ( Sessao::getExercicio() );
            break;
            case 6: //númerico(*, 2)
                $obAtributo = new Moeda();
                $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                $obAtributo->setNull( true );
            break;
            case 7: //texto longo
                $obAtributo = new TextBox();
                $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                $obAtributo->setNull( true );
                $obAtributo->setMaxLength( '' );
                $obAtributo->setSize( 60 );
            break;
        }
        $rsAtributo->proximo();
    }

    $obFormulario = new Formulario();
    $obFormulario->addComponente( $obAtributo );
    $obFormulario->montaInnerHTML();

    $stJs .= "document.getElementById('stAtributo').value = '';";
    $stJs .= "var html = document.getElementById('spnListaAtributos').innerHTML;";
    $stJs .= "document.getElementById('spnListaAtributos').innerHTML = html + '".$obFormulario->getHTML()."';";

        echo $stJs;
    break;
}

?>
