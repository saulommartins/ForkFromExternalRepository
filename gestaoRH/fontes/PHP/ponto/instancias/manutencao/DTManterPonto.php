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
    * Formulário
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$obTableTree = new Table();
$obTableTree->setSummary("Horário Padrão");

$horario               = $_GET["horario_padrao"];
$stCargaHorariaPadrao  = $_GET["carga_horaria_padrao"];
$stHorasFaltasAnuladas = $_GET["horas_faltas_anuladas"];
$stHorasAbonadas       = $_GET["horas_abonadas"];
$stJustificativaAfastamento = $_GET["justificativa_afastamento"];
$dtLote                     = $_GET["dtLote"];

list($entrada_1, $saida_1, $entrada_2, $saida_2) = explode("-", $horario);

$arDetalhes = array();
if (trim($entrada_1)!="" && trim($saida_1)!="") {
    $arDetalhes[0]["entrada_1"]  = trim($entrada_1);
    $arDetalhes[0]["saida_1"]    = trim($saida_1);
    $arDetalhes[0]["entrada_2"]  = trim($entrada_2);
    $arDetalhes[0]["saida_2"]    = trim($saida_2);
    $arDetalhes[0]["carga_horaria_padrao"]  = trim($stCargaHorariaPadrao);
    $arDetalhes[0]["horas_faltas_anuladas"] = trim($stHorasFaltasAnuladas);
    $arDetalhes[0]["horas_abonadas"]        = trim($stHorasAbonadas);
    $arDetalhes[0]["justificativa_afastamento"] = trim($stJustificativaAfastamento);
}

$recordSet = new RecordSet();
$recordSet->preenche($arDetalhes);

if ($recordSet->getNumLinhas() < 1) {
    $recordSet->add(array('mensagem' => "Não existe horário padrão cadastrado para o servidor para a data."));
    $obTableTree->setRecordset($recordSet);

    $obTableTree->Head->addCabecalho( 'Mensagem' , 70  );
    $obTableTree->Body->addCampo( 'mensagem', 'C' );
} else {
    $obTableTree = new Table();
    $obTableTree->setRecordset($recordSet);
    $obTableTree->setSummary("Horário Padrão");

    $obTableTree->Head->addCabecalho( "Entrada1"  , 10  );
    $obTableTree->Head->addCabecalho( "Saída1"    , 10  );
    $obTableTree->Head->addCabecalho( "Entrada2"  , 10  );
    $obTableTree->Head->addCabecalho( "Saída2"    , 10  );
    $obTableTree->Head->addCabecalho( "Horas/Dia" , 10  );
    if (trim($dtLote)=="") {
        $obTableTree->Head->addCabecalho( "Horas Faltas Anuladas" , 10  );
        $obTableTree->Head->addCabecalho( "Horas Abonadas" , 10  );
    } else {
        $obTableTree->Head->addCabecalho( "Justificativas/Afastamentos" , 20  );
    }

    $obTableTree->Body->addCampo( "entrada_1"            , "C" );
    $obTableTree->Body->addCampo( "saida_1"              , "C" );
    $obTableTree->Body->addCampo( "entrada_2"            , "C" );
    $obTableTree->Body->addCampo( "saida_2"              , "C" );
    $obTableTree->Body->addCampo( "carga_horaria_padrao" , "C" );
    if (trim($dtLote)=="") {
        $obTableTree->Body->addCampo( "horas_faltas_anuladas", "C" );
        $obTableTree->Body->addCampo( "horas_abonadas"       , "C" );
    } else {
        $obTableTree->Body->addCampo( "justificativa_afastamento" , "E" );
    }
}

$obTableTree->montaHTML();
echo $obTableTree->getHtml();
?>
