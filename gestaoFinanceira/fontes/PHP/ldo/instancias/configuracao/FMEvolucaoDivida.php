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
    * Página de formulário da evolução da dívida
    * Data de Criação   : 03/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GF_LDO_NEGOCIO.'RLDOEvolucaoDivida.class.php';
include_once CAM_GF_LDO_VISAO.'VLDOEvolucaoDivida.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOIndicadores.class.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';

$stAcao = $request->get('stAcao');

sistemaLegado::BloqueiaFrames(true,false);

ob_flush();

$pgOcul = 'OCEvolucaoDivida.php';

include_once 'JSEvolucaoDivida.js';

$arExercicio[1] = $_REQUEST['slExercicioLDO'];
$arExercicio[2] = $_REQUEST['slExercicioLDO'] + 1;
$arExercicio[3] = $_REQUEST['slExercicioLDO'] + 2;

foreach ($arExercicio AS $inId => $stExercicio) {
    $obTLDOIndicadores = new TLDOIndicadores;
    $stFiltro  = " WHERE exercicio = '".$stExercicio."'";
    $stFiltro .= "   AND cod_tipo_indicador = ".$_REQUEST['inCodSelic'];
    $obTLDOIndicadores->recuperaTodos($rsIndicadores, $stFiltro);
    if ($rsIndicadores->getNumLinhas() < 1) {
        SistemaLegado::alertaAviso("FLEvolucaoDivida.php?".Sessao::getId()."&stAcao=".$stAcao, 'Não existe Selic cadastrado para o exercício '.$stExercicio.'!',"","aviso", Sessao::getId(), "../");
    }
}

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PREvolucaoDivida.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia um objeto hidden para o cod_ppa
$obHdnCodPPA = new Hidden;
$obHdnCodPPA->setName ('inCodPPA');
$obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

//Instancia um objeto hidden para o ano
$obHdnAno = new Hidden;
$obHdnAno->setName ('inAno');
$obHdnAno->setValue($_REQUEST['slExercicioLDO']);

//Instancia um span para as dívidas
$obSpnTableDivida = new Span();
$obSpnTableDivida->setId('spnDividas');

//Instancia um span para os serviços das dívidas
$obSpnTableServico = new Span();
$obSpnTableServico->setId('spnServico');

//recupera os dados da divida para a lista
$obVLDOEvolucaoDivida = new VLDOEvolucaoDivida(new RLDOEvolucaoDivida());
$obVLDOEvolucaoDivida->listDividasLDO($rsDividas, $_REQUEST);

//adiciona a formatacao de moeda
$rsDividas->addFormatacao('valor_1','NUMERIC_BR');
$rsDividas->addFormatacao('valor_2','NUMERIC_BR');
$rsDividas->addFormatacao('valor_3','NUMERIC_BR');
$rsDividas->addFormatacao('valor_4','NUMERIC_BR');
$rsDividas->addFormatacao('valor_5','NUMERIC_BR');
$rsDividas->addFormatacao('valor_6','NUMERIC_BR');

$obValor1 = new Numerico;
$obValor1->setId              ('flValor1_[cod_tipo]_[bo_orcamento_1]');
$obValor1->setName            ('flValor1_[cod_tipo]_[bo_orcamento_1]');
$obValor1->setLabel           (true);
$obValor1->setClass           ('valor');
$obValor1->setValue           ('[valor_1]');
$obValor1->setMaxLength       (11);
$obValor1->setSize            (12);
$obValor1->setNegativo        (false);
$obValor1->obEvento->setOnBlur('recalcularValores(1,this.id);');

$obValor2 = new Numerico;
$obValor2->setId              ('flValor2_[cod_tipo]_[bo_orcamento_2]');
$obValor2->setName            ('flValor2_[cod_tipo]_[bo_orcamento_2]');
$obValor2->setLabel           (true);
$obValor2->setClass           ('valor');
$obValor2->setValue           ('[valor_2]');
$obValor2->setMaxLength       (11);
$obValor2->setSize            (12);
$obValor2->setNegativo        (false);
$obValor2->obEvento->setOnBlur('recalcularValores(2,this.id);');

$obValor3 = new Numerico;
$obValor3->setId              ('flValor3_[cod_tipo]_[bo_orcamento_3]');
$obValor3->setName            ('flValor3_[cod_tipo]_[bo_orcamento_3]');
$obValor3->setLabel           (true);
$obValor3->setClass           ('valor');
$obValor3->setValue           ('[valor_3]');
$obValor3->setMaxLength       (11);
$obValor3->setSize            (12);
$obValor3->setNegativo        (false);
$obValor3->obEvento->setOnBlur('recalcularValores(3,this.id);');

$obValor4 = new Numerico;
$obValor4->setId              ('flValor4_[cod_tipo]_[bo_orcamento_4]');
$obValor4->setName            ('flValor4_[cod_tipo]_[bo_orcamento_4]');
$obValor4->setLabel           (true);
$obValor4->setClass           ('valor');
$obValor4->setValue           ('[valor_4]');
$obValor4->setMaxLength       (11);
$obValor4->setSize            (12);
$obValor4->setNegativo        (false);
$obValor4->obEvento->setOnBlur('recalcularValores(4,this.id);');

$obValor5 = new Numerico;
$obValor5->setId              ('flValor5_[cod_tipo]_[bo_orcamento_5]');
$obValor5->setName            ('flValor5_[cod_tipo]_[bo_orcamento_5]');
$obValor5->setLabel           (true);
$obValor5->setClass           ('valor');
$obValor5->setValue           ('[valor_5]');
$obValor5->setMaxLength       (11);
$obValor5->setSize            (12);
$obValor5->setNegativo        (false);
$obValor5->obEvento->setOnBlur('recalcularValores(5,this.id);');

$obValor6 = new Numerico;
$obValor6->setId              ('flValor6_[cod_tipo]_[bo_orcamento_6]');
$obValor6->setName            ('flValor6_[cod_tipo]_[bo_orcamento_6]');
$obValor6->setLabel           (true);
$obValor6->setClass           ('valor');
$obValor6->setValue           ('[valor_6]');
$obValor6->setMaxLength       (11);
$obValor6->setSize            (12);
$obValor6->setNegativo        (false);
$obValor6->obEvento->setOnBlur('recalcularValores(6,this.id);');

//cria a tabela para as dividas
$obTableDividas = new Table;
$obTableDividas->setId         ('tableDivida');
$obTableDividas->setTitle      ('Dívidas');
$obTableDividas->setRecordset  ($rsDividas);
//$obTableDividas->setConditional(true, "#efefef");

$obTableDividas->Head->addCabecalho('Descrição', 35);
$obTableDividas->Head->addCabecalho('Saldo '.($_REQUEST['slExercicioLDO'] - 3) ,10);
$obTableDividas->Head->addCabecalho('Saldo '.($_REQUEST['slExercicioLDO'] - 2), 10);
$obTableDividas->Head->addCabecalho('Reestimativa '.($_REQUEST['slExercicioLDO'] - 1), 10);
$obTableDividas->Head->addCabecalho('Previsão '.($_REQUEST['slExercicioLDO']) ,10);
$obTableDividas->Head->addCabecalho('Previsão '.($_REQUEST['slExercicioLDO'] + 1), 10);
$obTableDividas->Head->addCabecalho('Previsão '.($_REQUEST['slExercicioLDO'] + 2), 10);

$obTableDividas->Body->addCampo('[especificacao]', 'E');
$obTableDividas->Body->addCampo($obValor1, 'D');
$obTableDividas->Body->addCampo($obValor2, 'D');
$obTableDividas->Body->addCampo($obValor3, 'D');
$obTableDividas->Body->addCampo($obValor4, 'D');
$obTableDividas->Body->addCampo($obValor5, 'D');
$obTableDividas->Body->addCampo($obValor6, 'D');

$obTableDividas->montaHTML();

$obSpnTableDivida->setValue($obTableDividas->getHtml());

//recupera os dados do serviço da dívida para a lista
$obVLDOEvolucaoDivida = new VLDOEvolucaoDivida(new RLDOEvolucaoDivida());
$obVLDOEvolucaoDivida->listServicosLDO($rsServicos, $_REQUEST);

//adiciona a formatacao de moeda
$rsServicos->addFormatacao('valor_1','NUMERIC_BR');
$rsServicos->addFormatacao('valor_2','NUMERIC_BR');
$rsServicos->addFormatacao('valor_3','NUMERIC_BR');
$rsServicos->addFormatacao('valor_4','NUMERIC_BR');
$rsServicos->addFormatacao('valor_5','NUMERIC_BR');
$rsServicos->addFormatacao('valor_6','NUMERIC_BR');

$obValorServico1 = new Numerico;
$obValorServico1->setId              ('flServico1_[cod_tipo]_[bo_orcamento_1]');
$obValorServico1->setName            ('flServico1_[cod_tipo]_[bo_orcamento_1]');
$obValorServico1->setLabel           (true);
$obValorServico1->setClass           ('valor');
$obValorServico1->setValue           ('[valor_1]');
$obValorServico1->setMaxLength       (11);
$obValorServico1->setSize            (14);
$obValorServico1->setNegativo        (false);
$obValorServico1->obEvento->setOnBlur('recalcularValores(1,this.id);');

$obValorServico2 = new Numerico;
$obValorServico2->setId              ('flServico2_[cod_tipo]_[bo_orcamento_2]');
$obValorServico2->setName            ('flServico2_[cod_tipo]_[bo_orcamento_2]');
$obValorServico2->setLabel           (true);
$obValorServico2->setClass           ('valor');
$obValorServico2->setValue           ('[valor_2]');
$obValorServico2->setMaxLength       (11);
$obValorServico2->setSize            (14);
$obValorServico2->setNegativo        (false);
$obValorServico2->obEvento->setOnBlur('recalcularValores(2,this.id);');

$obValorServico3 = new Numerico;
$obValorServico3->setId              ('flServico3_[cod_tipo]_[bo_orcamento_3]');
$obValorServico3->setName            ('flServico3_[cod_tipo]_[bo_orcamento_3]');
$obValorServico3->setLabel           (true);
$obValorServico3->setClass           ('valor');
$obValorServico3->setValue           ('[valor_3]');
$obValorServico3->setMaxLength       (11);
$obValorServico3->setSize            (14);
$obValorServico3->setNegativo        (false);
$obValorServico3->obEvento->setOnBlur('recalcularValores(3,this.id);');

$obValorServico4 = new Numerico;
$obValorServico4->setId              ('flServico4_[cod_tipo]_[bo_orcamento_4]');
$obValorServico4->setName            ('flServico4_[cod_tipo]_[bo_orcamento_4]');
$obValorServico4->setLabel           (true);
$obValorServico4->setClass           ('valor');
$obValorServico4->setValue           ('[valor_4]');
$obValorServico4->setMaxLength       (11);
$obValorServico4->setSize            (14);
$obValorServico4->setNegativo        (false);
$obValorServico4->obEvento->setOnBlur('recalcularValores(4,this.id);');

$obValorServico5 = new Numerico;
$obValorServico5->setId              ('flServico5_[cod_tipo]_[bo_orcamento_5]');
$obValorServico5->setName            ('flServico5_[cod_tipo]_[bo_orcamento_5]');
$obValorServico5->setLabel           (true);
$obValorServico5->setClass           ('valor');
$obValorServico5->setValue           ('[valor_5]');
$obValorServico5->setMaxLength       (11);
$obValorServico5->setSize            (14);
$obValorServico5->setNegativo        (false);
$obValorServico5->obEvento->setOnBlur('recalcularValores(5,this.id);');

$obValorServico6 = new Numerico;
$obValorServico6->setId              ('flServico6_[cod_tipo]_[bo_orcamento_6]');
$obValorServico6->setName            ('flServico6_[cod_tipo]_[bo_orcamento_6]');
$obValorServico6->setLabel           (true);
$obValorServico6->setClass           ('valor');
$obValorServico6->setValue           ('[valor_6]');
$obValorServico6->setMaxLength       (11);
$obValorServico6->setSize            (14);
$obValorServico6->setNegativo        (false);
$obValorServico6->obEvento->setOnBlur('recalcularValores(6,this.id);');

//cria a tabela para os serviços das dívidas
$obTableServicos = new Table;
$obTableServicos->setId         ('tableServico');
$obTableServicos->setTitle      ('Serviço da Dívida');
$obTableServicos->setRecordset  ($rsServicos);
//$obTableServicos->setConditional(true, "#efefef");

$obTableServicos->Head->addCabecalho('Descrição', 35);
$obTableServicos->Head->addCabecalho('Realizado '.($_REQUEST['slExercicioLDO'] - 3) ,10);
$obTableServicos->Head->addCabecalho('Realizado '.($_REQUEST['slExercicioLDO'] - 2), 10);
$obTableServicos->Head->addCabecalho('Reestimativa '.($_REQUEST['slExercicioLDO'] - 1), 10);
$obTableServicos->Head->addCabecalho('Previsão '.($_REQUEST['slExercicioLDO']), 10);
$obTableServicos->Head->addCabecalho('Previsão '.($_REQUEST['slExercicioLDO'] + 1), 10);
$obTableServicos->Head->addCabecalho('Previsão '.($_REQUEST['slExercicioLDO'] + 2), 10);

$obTableServicos->Body->addCampo('[especificacao]', 'E');
$obTableServicos->Body->addCampo($obValorServico1, 'D');
$obTableServicos->Body->addCampo($obValorServico2, 'D');
$obTableServicos->Body->addCampo($obValorServico3, 'D');
$obTableServicos->Body->addCampo($obValorServico4, 'D');
$obTableServicos->Body->addCampo($obValorServico5, 'D');
$obTableServicos->Body->addCampo($obValorServico6, 'D');

$obTableServicos->montaHTML();

$obSpnTableServico->setValue($obTableServicos->getHtml());

//Instancia um objeto Formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCodPPA);
$obFormulario->addHidden        ($obHdnAno);

$obFormulario->addTitulo        ('Dívidas');
$obFormulario->addSpan          ($obSpnTableDivida);
$obFormulario->addTitulo        ('Serviço da Dívida');
$obFormulario->addSpan          ($obSpnTableServico);

$obFormulario->Cancelar         ('FLEvolucaoDivida.php');
$obFormulario->show             ();

$jsOnload = 'formataTableDivida();';
$jsOnload.= 'LiberaFrames(true,false);';

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
