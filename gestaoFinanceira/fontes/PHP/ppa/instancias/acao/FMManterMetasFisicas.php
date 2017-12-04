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
 * Página de Formulário Lançar Metas Fisicas Realizadas.
 * Data de Criacao: 12/04/2016

 * @author Analista : Valtair Santos
 * @author Desenvolvedor : Michel Teixeira
 * @ignore

 $Id: FMManterMetasFisicas.php 65085 2016-04-22 13:41:21Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAAcaoRecurso.class.php';

# Define o nome dos arquivos PHP
$stPrograma = 'ManterMetasFisicas';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJs       = 'JS'.$stPrograma.'.js';

include_once $pgJs;

$stAcao = $request->get('stAcao');

$obTPPAAcaoRecurso = new TPPAAcaoRecurso;
$obTPPAAcaoRecurso->recuperaRecursosAcaoMetasFisicas($rsRecursos, $stFiltro);

$stAno = "";
$stPPA = "";

$arParametrosMetas = array();

while (!$rsRecursos->eof()) {
    $arRecursoAno = array();

    $inCodAcao       = $rsRecursos->getCampo('cod_acao');
    $inCodRecurso    = $rsRecursos->getCampo('cod_recurso');
    $stTimestampAcao = $rsRecursos->getCampo('timestamp_acao_dados');

    $arRecursoAno['cod_acao']             = $inCodAcao;
    $arRecursoAno['cod_recurso']          = $inCodRecurso;
    $arRecursoAno['timestamp_acao_dados'] = $stTimestampAcao;
    $arRecursoAno['num_acao']             = $rsRecursos->getCampo('num_acao');
    $arRecursoAno['nom_cod_recurso']      = $rsRecursos->getCampo('nom_cod_recurso');

    for ($i = 1; $i<= 4; $i++) {
        $ano = 'ano'.$i;
        $arRecursoAno['stExercicio_'.$i]      = $rsRecursos->getCampo($ano);
        $arRecursoAno['flQuantidade_'.$i]     = $rsRecursos->getCampo($ano.'_qtd');
        $arRecursoAno['flValorTotal_'.$i]     = $rsRecursos->getCampo($ano.'_valor');
        $arRecursoAno['flValorRealizado_'.$i] = $rsRecursos->getCampo($ano.'_realizada');
        $arRecursoAno['stJustificativa_'.$i]  = $rsRecursos->getCampo($ano.'_justificativa');

        if(Sessao::getExercicio() == $rsRecursos->getCampo($ano))
            $stAno = $i;
    }

    $arRecursoAno['flQuantidade_total'] = $rsRecursos->getCampo('total_qtd');
    $arRecursoAno['flValorTotal_total'] = $rsRecursos->getCampo('total_valor');
    $arRecursoAno['boAlterado']         = FALSE;

    $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso] = $arRecursoAno;

    $stPPA = $rsRecursos->getCampo('nom_ppa');

    $rsRecursos->proximo();
}

$rsRecursos->addFormatacao('ano1_valor' , 'NUMERIC_BR');
$rsRecursos->addFormatacao('ano2_valor' , 'NUMERIC_BR');
$rsRecursos->addFormatacao('ano3_valor' , 'NUMERIC_BR');
$rsRecursos->addFormatacao('ano4_valor' , 'NUMERIC_BR');
$rsRecursos->addFormatacao('total_valor', 'NUMERIC_BR');

Sessao::write('arParametrosMetas', $arParametrosMetas);

// Definição do form
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setId( "stCtrl" );

$obTxtPPA = new TextBox();
$obTxtPPA->setID('stPPA');
$obTxtPPA->setName('stPPA');
$obTxtPPA->setRotulo('PPA');
$obTxtPPA->setValue($stPPA);
$obTxtPPA->setLabel(TRUE);

$obTxtExercicioAno = new TextBox();
$obTxtExercicioAno->setID('stExercicioAno');
$obTxtExercicioAno->setName('stExercicioAno');
$obTxtExercicioAno->setRotulo('Exercício');
$obTxtExercicioAno->setValue(Sessao::getExercicio().' - Ano '.$stAno);
$obTxtExercicioAno->setLabel(TRUE);

// Define botoes de ação.
$obBtnOK = new Ok(true);

$arBotoes = array($obBtnOK);

$obTblRecursos = new TableTree;
$obTblRecursos->setId('obTblRecursos');
$obTblRecursos->setSummary('Lista de Ações - Recursos');
$obTblRecursos->setArquivo('OCManterMetasFisicas.php');
$obTblRecursos->setRecordSet($rsRecursos);
$obTblRecursos->setParametros(array('cod_acao','cod_recurso'));
$obTblRecursos->setComplementoParametros('stCtrl=montaMetaFisica');
$obTblRecursos->Head->addCabecalho ('Ação'          ,  7);
$obTblRecursos->Head->addCabecalho ('Recurso'       , 40);
$obTblRecursos->Head->addCabecalho ('Valor Ano 1'   ,  9);
$obTblRecursos->Head->addCabecalho ('Valor Ano 2'   ,  9);
$obTblRecursos->Head->addCabecalho ('Valor Ano 3'   ,  9);
$obTblRecursos->Head->addCabecalho ('Valor Ano 4'   ,  9);
$obTblRecursos->Head->addCabecalho ('Total Recurso' , 10);
$obTblRecursos->Body->addCampo     ('[num_acao]'       , 'C');
$obTblRecursos->Body->addCampo     ('[nom_cod_recurso]', 'E');
$obTblRecursos->Body->addCampo     ('[ano1_valor]'     , 'D');
$obTblRecursos->Body->addCampo     ('[ano2_valor]'     , 'D');
$obTblRecursos->Body->addCampo     ('[ano3_valor]'     , 'D');
$obTblRecursos->Body->addCampo     ('[ano4_valor]'     , 'D');
$obTblRecursos->Body->addCampo     ('[total_valor]'    , 'D');

$obTblRecursos->montaHTML(FALSE);
$stLstRecursos = $obTblRecursos->getHtml();

$obSpanListaRecurso = new Span();
$obSpanListaRecurso->setID('spnListaRecurso');
$obSpanListaRecurso->setValue($stLstRecursos);

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnCtrl);
$obFormulario->addComponente($obTxtPPA);
$obFormulario->addComponente($obTxtExercicioAno);
$obFormulario->addSpan  ($obSpanListaRecurso);
$obFormulario->defineBarra($arBotoes);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>