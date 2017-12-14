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
    * Página de Filtro do Relatório Empenhos por Modalidade
    * Data de Criação   : 22/03/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM

    $Id: FLRelatorioEmpenhoModalidade.php 64778 2016-03-31 13:51:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";

$stPrograma    = "RelatorioEmpenhoModalidade";
$pgOcul        = "OC".$stPrograma.".php";
$pgGeraRel     = "OCGera".$stPrograma.".php";

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

Sessao::remove('arEntidades');
while ( !$rsEntidades->eof() ) {
    $arEntidades[$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
Sessao::write('arEntidades', $arEntidades);
$rsEntidades->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/".$pgOcul );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio() );
$obPeriodicidade->setValidaExercicio( true );
$obPeriodicidade->setNull           ( false );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
$rsRecordset = new RecordSet;
if ($rsEntidades->getNumLinhas()==1) {
    $rsRecordset = $rsEntidades;
    $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

include_once ( CAM_GA_ADM_NEGOCIO.'RCadastroDinamico.class.php' );
$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro( 1 );
$obRCadastroDinamico->obRModulo->setCodModulo( 10 );
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosDinamicos );

$arAtributo = array();
foreach($rsAtributosDinamicos->getElementos() AS $atributos){
    if( stripos($atributos['nom_atributo'], 'modalidade') !== false ){
        $arCodAtributos = explode(',', $atributos['valor_padrao']);
        $arNomAtributos = explode('[][][]', $atributos['valor_padrao_desc']);
        foreach($arCodAtributos AS $key => $inCodAtributo){
            $arNomModalidade[] = $inCodAtributo."-".$arNomAtributos[$key];
        }

        $arNomAtributos = implode(',', $arNomModalidade);

        $atributos['valor_padrao'] = $arNomAtributos;
        
        $arAtributo[] = $atributos;
    }
}
$rsAtributosDinamicos = new RecordSet();
$rsAtributosDinamicos->preenche($arAtributo);

$obMontaAtributos = new MontaAtributos();
$obMontaAtributos->setName( 'atributos_' );
$obMontaAtributos->setRecordSet( $rsAtributosDinamicos );
$obMontaAtributos->setTitulo( 'Atributos' );

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades );
$obFormulario->addComponente( $obPeriodicidade );
if ( $rsAtributosDinamicos->getNumLinhas() > 0 )
    $obMontaAtributos->geraFormulario( $obFormulario );

$obFormulario->addTitulo    ( "Assinaturas" );
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
