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

$stPrograma = 'DepreciacaoAutomatica';
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = 'PR'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');

$obRdAnularDepreciacaoSim = new Radio;
$obRdAnularDepreciacaoSim->setObrigatorio(true);
$obRdAnularDepreciacaoSim->setRotulo ('Anulação');
$obRdAnularDepreciacaoSim->setName   ('boAnulacao');
$obRdAnularDepreciacaoSim->setId     ('boAnulacao');
$obRdAnularDepreciacaoSim->setTitle  ('Anula todos os bens já depreciados por competência');
$obRdAnularDepreciacaoSim->setValue  ('true');
$obRdAnularDepreciacaoSim->setLabel  ('Sim');

$obRdAnularDepreciacaoNao = new Radio;
$obRdAnularDepreciacaoNao->setName   ('boAnulacao');
$obRdAnularDepreciacaoSim->setId     ('boAnulacao');
$obRdAnularDepreciacaoNao->setValue  ('false');
$obRdAnularDepreciacaoNao->setLabel  ('Não');
$obRdAnularDepreciacaoNao->setChecked('true');

$obSpnClassificacao = new Span;
$obSpnClassificacao->setId('spnClassificacao');

$arDescCompetencia = array(
    1 => 'Mês',
    2 => 'Bimestre',
    3 => 'Trimestre',
    4 => 'Quadrimestre',
    6 => 'Semestre',
    7 => 'Ano'
);

$arOrdinal = array('Primeiro', 'Segundo', 'Terceiro', 'Quarto', 'Quinto','Sexto');
$arCompetencia = array();

$obSlcCompentecia = new SelectMeses;
$obSlcCompentecia->setName('inCompetencia');
$obSlcCompentecia->setRotulo('Competência');
$obSlcCompentecia->setTitle('Selecione a competência em que ocorrerá a depreciação.');
$obSlcCompentecia->setValue(date('n'));
$obSlcCompentecia->setNull(false);

$obIntExercicio = new Exercicio;
$obIntExercicio->setName('inExercicio');
$obIntExercicio->setValue(Sessao::getExercicio());
$obIntExercicio->setNull(false);
$obIntExercicio->setReadOnly(true);

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("telaPrincipal");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obTxtMotivo = new TextArea;
$obTxtMotivo->setName('stMotivo');
$obTxtMotivo->setRotulo('Motivo');
$obTxtMotivo->setCols(25);
$obTxtMotivo->setRows(4);
$obTxtMotivo->setMaxCaracteres(100);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

$obFormulario->addTitulo('Processar Depreciação');
$obFormulario->agrupaComponentes(array($obRdAnularDepreciacaoSim,$obRdAnularDepreciacaoNao));
$obFormulario->addSpan($obSpnClassificacao);

$obFormulario->agrupaComponentes(array($obSlcCompentecia, $obIntExercicio));
$obFormulario->addComponente($obTxtMotivo);
$obFormulario->Ok(true);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>