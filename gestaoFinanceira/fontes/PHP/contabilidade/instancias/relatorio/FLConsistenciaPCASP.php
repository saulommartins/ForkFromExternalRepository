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
    * Página de Filtro para relatorio Consistência PCASP
    * Data de Criação   : 25/09/2013

    * @author Analista:  Sergio Luiz dos Santos
    * @author Desenvolvedor: Jean Silva

    * @ignore

    * $Id: FLConsistenciaPCASP.php 52880 2012-08-28 19:15:58Z tonismar $

    * Casos de uso: uc-02.02.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php"          );

$stPrograma = "ConsistenciaPCASP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::write('filtroRegistro', array());
Sessao::write('rsRecordSet', '');

$rsRecordset                     = new RecordSet;
$obRContabilidadePlanoBanco      = new RContabilidadePlanoBanco;

$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->listarGrupos( $rsGrupos );

$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( CAM_GF_CONT_INSTANCIAS."relatorio/OCGeraRelatorioConsistenciaPCASP.php" );
$obForm->setTarget( "telaPrincipal" );

$obHdnValidacao = new HiddenEval;
$obHdnValidacao->setName("boValidacao");
$obHdnValidacao->setValue( " " ); //Preenchido a partir do JS

if ( Sessao::getExercicio() > '2012' ) {
    $stEval = "
    if ( !document.getElementById('stEstiloContaAnalitica').checked && !document.getElementById('stEstiloContaSintetica').checked ) {
        erro = true;
        mensagem += '@Selecione o tipo do relatório';
    }";
} else {
    $stEval = "
        if ( !document.getElementById('stEstiloContaAnalitica').checked && !document.getElementById('stEstiloContaSintetica').checked ) {
            erro = true;
            mensagem += '@Selecione o tipo do relatório';
        }
        if (erro == false) {
            var xvalida = false;
            for (i=0 ; i<document.frm.elements.length ; i++) {
                if ( typeof(document.frm.elements[i]) == 'object' ) {
                    if (document.frm.elements[i].type == 'checkbox') {
                        if (document.frm.elements[i].name.substring(0,6) == 'grupo_') {
                            if (document.frm.elements[i].checked == true) {
                                xvalida = true;
                            }
                        }
                    }
                }
            }
            if (xvalida == false) {
                mensagem += '@Selecione ao menos um grupo!';
                erro = true;
            }
         } ";
}

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( $stEval  );

if ( !Sessao::getExercicio() > '2012' ) {
    while (!$rsGrupos->EOF()) {
        if($rsGrupos->getCampo('nom_conta') == "(R) DEDUCOES DA RECEITA")
            $stNomConta = "(R) Deduções da Receita";
        else
            $stNomConta = ucfirst(strtolower($rsGrupos->getCampo('nom_conta')));
        $objeto = 'obChkGrupo_'.$rsGrupos->getCampo('cod_grupo');
        $$objeto = new Checkbox;
        $$objeto->setName    ( 'grupo_'.$rsGrupos->getCampo('cod_grupo')   );
        $$objeto->setId      ( 'grupo_'.$rsGrupos->getCampo('cod_grupo')   );
        $$objeto->setValue   (  $rsGrupos->getCampo('cod_grupo')        );
        $$objeto->setLabel   (  $stNomConta        );
        $$objeto->setRotulo  ( 'Grupos de contas'  );
        $$objeto->setChecked ( true               );
        $rsGrupos->proximo();
    }
}

// define objeto Data
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setNull            (false );
$obPeriodo->setValue           ( 4);

include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php");
$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario( $obForm );

// Define Objeto Radio Para Tipo de conta
$obRdEstiloContaSintetica = new Radio;
$obRdEstiloContaSintetica->setName   ( "stEstiloConta"  );
$obRdEstiloContaSintetica->setId     ( "stEstiloContaAnalitica"  );
$obRdEstiloContaSintetica->setValue  ( "S"              );
$obRdEstiloContaSintetica->setRotulo ( "*Tipo de Relatório" );
$obRdEstiloContaSintetica->setLabel  ("Sintético"       );

$obRdEstiloContaAnalitica = new Radio;
$obRdEstiloContaAnalitica->setName ( "stEstiloConta" );
$obRdEstiloContaAnalitica->setId   ( "stEstiloContaSintetica" );
$obRdEstiloContaAnalitica->setValue( "A"           );
$obRdEstiloContaAnalitica->setLabel( "Analítico"   );
$obRdEstiloContaAnalitica->setChecked( true );

// Define Objeto Radio para opção de exibição da coluna código estrutural
$radioEstruturalSim = new Radio;
$radioEstruturalSim->setName ( "stEstrutural" );
$radioEstruturalSim->setId   ( "stEstrutural" );
$radioEstruturalSim->setValue( "S" );
$radioEstruturalSim->setLabel( "Sim" );

$radioEstruturalSim->setRotulo( "Imprimir código estrutural" );

$radioEstruturalNao = new Radio;
$radioEstruturalNao->setName ( "stEstrutural" );
$radioEstruturalNao->setId   ( "stEstrutural" );
$radioEstruturalNao->setValue( "N" );
$radioEstruturalNao->setLabel( "Não" );

if ( Sessao::getExercicio() > '2012' ) {
    $radioEstruturalSim->setChecked( true );
    $radioEstruturalNao->setChecked( false );
} else {
    $radioEstruturalSim->setChecked( false );
    $radioEstruturalNao->setChecked( true );
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.22');
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnEval, true );

$obFormulario->addTitulo( "Dados para Filtro" );

$rsGrupos->setPrimeiroElemento();
if ( !Sessao::getExercicio() > '2012' ) {
    while ( !$rsGrupos->EOF() ) {
        $objeto = 'obChkGrupo_'.$rsGrupos->getCampo('cod_grupo');
        $obFormulario->addComponente( $$objeto );
        $rsGrupos->proximo();
    }
}

$obFormulario->agrupaComponentes( array( $radioEstruturalSim, $radioEstruturalNao) );
$obFormulario->addComponente( $obEntidadeUsuario       );
$obFormulario->addComponente( $obPeriodo            );
$obFormulario->agrupaComponentes( array($obRdEstiloContaSintetica, $obRdEstiloContaAnalitica) );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
