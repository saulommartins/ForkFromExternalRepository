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
 * Página de Filtros do Relatório Mapa de Recursos
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoRelatorioBalanceteDespesa.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoConfiguracao.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';

$rsRecordset = new RecordSet();
$rsEntidades = new RecordSet();

$obRegra = new ROrcamentoRelatorioBalanceteDespesa;
$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obRegra->obREntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRegra->obREntidade->setExercicio(Sessao::getExercicio());
$obRegra->obREntidade->listarUsuariosEntidade($rsEntidades, " ORDER BY cod_entidade");
while (!$rsEntidades->eof()) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo('nom_cgm');
    $rsEntidades->proximo();
}
Sessao::write('filtroNomRelatorio', $arNomFiltro);
$rsEntidades->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction("OCGeraMapaRecursos.php");
$obForm->setTarget("telaPrincipal");

$obDataLimite = new Data;
$obDataLimite->setName("stDataFinal");
$obDataLimite->setRotulo("Posição em");
$obDataLimite->setTitle("Informe a data limite para o filtro");
$obDataLimite->setNull(false);

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName('inCodEntidade');
$obCmbEntidades->setRotulo("Entidades");
$obCmbEntidades->setTitle("Selecione as entidades que deseja pesquisar.");
$obCmbEntidades->setNull(false);

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1('cod_entidade');
$obCmbEntidades->setCampoDesc1('nom_cgm');
$obCmbEntidades->SetRecord1($rsEntidades);

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2('inCodEntidade');
$obCmbEntidades->setCampoId2('cod_entidade');
$obCmbEntidades->setCampoDesc2('nom_cgm');
$obCmbEntidades->SetRecord2( $rsRecordset );

$obTxtCodRecursoInicial = new TextBox;
$obTxtCodRecursoInicial->setRotulo("Código Recurso");
$obTxtCodRecursoInicial->setTitle("Informe o intervalo do código do recurso para o filtro.");
$obTxtCodRecursoInicial->setName("stCodRecursoInicial");
$obTxtCodRecursoInicial->setSize(10);
$obTxtCodRecursoInicial->setMaxLength(5);

$obLblAte = new Label;
$obLblAte->setValue(' Até ');

$obTxtCodRecursoFinal = new TextBox;
$obTxtCodRecursoFinal->setRotulo("Código Recurso Final");
$obTxtCodRecursoFinal->setTitle("Informe o código do recurso final para filtro.");
$obTxtCodRecursoFinal->setName("stCodRecursoFinal");
$obTxtCodRecursoFinal->setSize(10);
$obTxtCodRecursoFinal->setMaxLength(5);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm );
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obDataLimite);
$obFormulario->agrupaComponentes(array($obTxtCodRecursoInicial, $obLblAte, $obTxtCodRecursoFinal));

$obFormulario->OK();
$obFormulario->show();

?>
