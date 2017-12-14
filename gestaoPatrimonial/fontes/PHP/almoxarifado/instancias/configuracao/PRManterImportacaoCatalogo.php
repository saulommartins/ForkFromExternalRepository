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
  * Data de criação : 13/12/2005

   * @autor Analista    : Diego Victoria
   * @autor Programador : Fernando Zank Correa Evangelista

   Caso de uso : 03.03.12

 */

/*
$Log$
Revision 1.14  2006/07/06 14:00:33  diego
Retirada tag de log com erro.

Revision 1.13  2006/07/06 12:09:53  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/arquivos/ArquivoTexto.class.php';
include_once( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoImportacaoCatalogo.class.php"     );
include_once(CLA_IMPORTADOR);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterImportacaoCatalogo";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

$stCaminhoUpload = "../../tmp/";
foreach ($_FILES as $chave) {
    if (($chave['type'] == "text/x-comma-separated-values" || $chave['type'] == "text/comma-separated-values") && $chave['error'] != 1) {
        move_uploaded_file($chave['tmp_name'],$stCaminhoUpload.$chave['name']);
    } else {
        SistemaLegado::alertaAviso($PHP_SELF,"Erro na Importação de Catálogos! (Os arquivos importados devem ter a extensão CSV e seguir sua devida formatação, verifique a extensão e formatação dos mesmos !)","generica","erro");
        Break;
    }
}
@unlink($stCaminhoUpload."tipoDeItem.csv");
$arqTipoItem = new ArquivoTexto($stCaminhoUpload."tipoItem.csv");
$arqTipoItem->addLinha('1;"Material"');
$arqTipoItem->addLinha('2;"Perecível"');
$arqTipoItem->addLinha('3;"Serviço"');
$arqTipoItem->addLinha('4;"Patrimônio"');
$arqTipoItem->Gravar();

@unlink($stCaminhoUpload."unidadeMedida.csv");
$arqUnidadeMedida = new ArquivoTexto($stCaminhoUpload."unidadeMedida.csv");
$arqUnidadeMedida->addLinha('00;"Indefinido"');
$arqUnidadeMedida->addLinha('11;"Segundo(s)"');
$arqUnidadeMedida->addLinha('12;"Minuto(s)"');
$arqUnidadeMedida->addLinha('13;"Hora(s)"');
$arqUnidadeMedida->addLinha('14;"Dia(s)"');
$arqUnidadeMedida->addLinha('15;"Semana(s)"');
$arqUnidadeMedida->addLinha('16;"Mês(es)"');
$arqUnidadeMedida->addLinha('17;"Ano(s)"');
$arqUnidadeMedida->addLinha('21;"Metro(s) Quadrado(s)"');
$arqUnidadeMedida->addLinha('22;"Quilômetro(s) Quadrado(s)"');
$arqUnidadeMedida->addLinha('23;"Hectare(s)"');
$arqUnidadeMedida->addLinha('31;"Grama(s)"');
$arqUnidadeMedida->addLinha('32;"Quilograma(s)"');
$arqUnidadeMedida->addLinha('33;"Tonelada(s)"');
$arqUnidadeMedida->addLinha('41;"Milímetro(s)"');
$arqUnidadeMedida->addLinha('42;"Centímetro(s)"');
$arqUnidadeMedida->addLinha('43;"Metro(s)"');
$arqUnidadeMedida->addLinha('44;"Quilômetro(s)"');
$arqUnidadeMedida->addLinha('51;"Litro(s)"');
$arqUnidadeMedida->addLinha('52;"Metro(s) Cúbico(s)"');
$arqUnidadeMedida->addLinha('71;"Unidade"');
$arqUnidadeMedida->addLinha('72;"Peça"');
$arqUnidadeMedida->addLinha('73;"Caixa"');
$arqUnidadeMedida->addLinha('74;"Dúzia"');
$arqUnidadeMedida->addLinha('75;"Molho"');
$arqUnidadeMedida->addLinha('76;"Pacote"');
$arqUnidadeMedida->addLinha('77;"Fardo"');
$arqUnidadeMedida->addLinha('78;"Resma"');
$arqUnidadeMedida->addLinha('79;"Lata"');
$arqUnidadeMedida->Gravar();

@unlink($stCaminhoUpload."tipoAtributo.csv");
$arqTipoAtributo = new ArquivoTexto($stCaminhoUpload."tipoAtributo.csv");
$arqTipoAtributo->addlinha('1;"Numerico"');
$arqTipoAtributo->addlinha('2;"Texto"');
$arqTipoAtributo->addlinha('3;"Lista"');
$arqTipoAtributo->addlinha('4;"Lista Múltipla"');
$arqTipoAtributo->addlinha('5;"Data"');
$arqTipoAtributo->Gravar();

$obImportacaoCatalogo = new Importador;
//Arquivo tipoItem.csv
$obImportacaoCatalogo->addArquivo($stCaminhoUpload."tipoItem.csv");
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna(';');
$obImportacaoCatalogo->roArquivo->setDelimitadorTexto('"');
$obImportacaoCatalogo->roArquivo->addColuna("Código","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(1);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Descrição","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(10);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");

//Arquivo unidadeMedida.csv
$obImportacaoCatalogo->addArquivo($stCaminhoUpload."unidadeMedida.csv");
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna(';');
$obImportacaoCatalogo->roArquivo->setDelimitadorTexto('"');
$obImportacaoCatalogo->roArquivo->addColuna("Código","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(1);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Descrição","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(30);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");

$obImportacaoCatalogo->roArquivo->setDelimitadorTexto(stripslashes($stDelimitadorTexto));
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna(stripslashes($stDelimitadorColuna));

//tipo Atributo.csv
$obImportacaoCatalogo->addArquivo($stCaminhoUpload."tipoAtributo.csv");
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna(';');
$obImportacaoCatalogo->roArquivo->setDelimitadorTexto('"');
$obImportacaoCatalogo->roArquivo->addColuna("Código","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(1);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Descrição","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(15);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");

//arquivo catalogo.csv
$obImportacaoCatalogo->addArquivo($stCaminhoUpload.$_FILES['stArquivoCatalogo']['name']);
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna($obImportacaoCatalogo->roArquivo->getDelimitadorColuna());
$obImportacaoCatalogo->roArquivo->setDelimitadorTexto($obImportacaoCatalogo->roArquivo->getDelimitadorTexto());
$obImportacaoCatalogo->roArquivo->addColuna("Descrição do Catálogo","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(160);
$obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Descrição do Nível","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(160);
$obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Máscara do Nível","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");

//arquivo classificacao.csv
$obImportacaoCatalogo->addArquivo($stCaminhoUpload.$_FILES['stArquivoClassificacao']['name']);
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna($obImportacaoCatalogo->roArquivo->getDelimitadorColuna());
$obImportacaoCatalogo->roArquivo->setDelimitadorTexto($obImportacaoCatalogo->roArquivo->getDelimitadorTexto());
$obImportacaoCatalogo->roArquivo->addColuna("Código Estrutural","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(160);
$obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Descrição da Classificação","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(160);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");

//arquivo item.csv
$obImportacaoCatalogo->addArquivo($stCaminhoUpload.$_FILES['stArquivoItem']['name']);
$obImportacaoCatalogo->roArquivo->setDelimitadorColuna($obImportacaoCatalogo->roArquivo->getDelimitadorColuna());
$obImportacaoCatalogo->roArquivo->setDelimitadorTexto($obImportacaoCatalogo->roArquivo->getDelimitadorTexto());
$obImportacaoCatalogo->roArquivo->addColuna("Código do Item","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(10);
$obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Código Estrutural","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(160);
$obImportacaoCatalogo->roArquivo->roColuna->setChaveEstrangeira($_FILES['stArquivoClassificacao']['name'],"Código Estrutural");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Descrição do Item","CARACTER");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(1500);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Código da Unidade de Medida","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(2);
$obImportacaoCatalogo->roArquivo->roColuna->setChaveEstrangeira("unidadeMedida.csv","Código");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Código do Tipo de Item","INTEIRO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(1);
$obImportacaoCatalogo->roArquivo->roColuna->setChaveEstrangeira("tipoItem.csv","Código");
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
$obImportacaoCatalogo->roArquivo->addColuna("Estoque Mínimo","NUMERICO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(14.4);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("FALSE");
$obImportacaoCatalogo->roArquivo->addColuna("Estoque Máximo","NUMERICO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(14.4);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("FALSE");
$obImportacaoCatalogo->roArquivo->addColuna("Ponto de Pedido","NUMERICO");
$obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(14.4);
$obImportacaoCatalogo->roArquivo->roColuna->setRequerido("FALSE");

if ($_FILES['stArquivoAtributo']['size'] != 0) {

    //arquivo atributo
    $obImportacaoCatalogo->addArquivo($stCaminhoUpload.$_FILES['stArquivoAtributo']['name']);
    $obImportacaoCatalogo->roArquivo->setDelimitadorColuna($obImportacaoCatalogo->roArquivo->getDelimitadorColuna());
    $obImportacaoCatalogo->roArquivo->setDelimitadorTexto($obImportacaoCatalogo->roArquivo->getDelimitadorTexto());
    $obImportacaoCatalogo->roArquivo->addColuna("Nome do Atributo","CARACTER");
    $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(80);
    $obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
    $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
    $obImportacaoCatalogo->roArquivo->addColuna("Código do Tipo do Atributo","INTEIRO");
    $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(2);
    $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
    $obImportacaoCatalogo->roArquivo->addColuna("Atributo pode ser Nulo","BOOLEAN");
    $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
    $obImportacaoCatalogo->roArquivo->addColuna("Ajuda do Atributo","CARACTER");
    $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(80);
    $obImportacaoCatalogo->roArquivo->addColuna("Máscara","CARACTER");
    $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(40);
    $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("FALSE");
}
if ($_FILES['stArquivoItemAtributos']['size'] != 0) {
    if ($_FILES['stArquivoAtributo']['size'] == 0) {
        SistemaLegado::alertaAviso($pgForm,"Erro na Importação de Catálogos !(O arquivo Atributo não pode estar vazio se o arquivo itemAtributo estiver preenchido !)","generica","erro", Sessao::getId(), "../");
    } else {
        //arquivo ItemAtributos
        $obImportacaoCatalogo->addArquivo($stCaminhoUpload.$_FILES['stArquivoItemAtributos']['name']);
        $obImportacaoCatalogo->roArquivo->setDelimitadorColuna($obImportacaoCatalogo->roArquivo->getDelimitadorColuna());
        $obImportacaoCatalogo->roArquivo->setDelimitadorTexto($obImportacaoCatalogo->roArquivo->getDelimitadorTexto());
        $obImportacaoCatalogo->roArquivo->addColuna("Código do Item","INTEIRO");
        $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(10);
        $obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
        $obImportacaoCatalogo->roArquivo->roColuna->setChaveEstrangeira($_FILES['stArquivoItem']['name'],"Código do Item");
        $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
        $obImportacaoCatalogo->roArquivo->addColuna("Nome do Atributo","CARACTER");
        $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(80);
        $obImportacaoCatalogo->roArquivo->roColuna->setChavePrimaria("TRUE");
        $obImportacaoCatalogo->roArquivo->roColuna->setChaveEstrangeira($_FILES['stArquivoAtributo']['name'],"Nome do Atributo");
        $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
        $obImportacaoCatalogo->roArquivo->addColuna("Valor do Atributo","CARACTER");
        $obImportacaoCatalogo->roArquivo->roColuna->setTamanhoMaximo(500);
        $obImportacaoCatalogo->roArquivo->roColuna->setRequerido("TRUE");
    }
}

$obErro = $obImportacaoCatalogo->Show();

if ($obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgForm,"Erro na Importação de Catálogos !(".$obErro->getDescricao()." !)","generica","erro", Sessao::getId(), "../");
}

if ($stDescricaoCatalogo == $obImportacaoCatalogo->arArquivos[3]->rsRecordSet->getCampo('Descrição do Catálogo')) {

    $obRegra = new RAlmoxarifadoImportacaoCatalogo;
    $obRegra->setRecordSetCatalogo($obImportacaoCatalogo->arArquivos[3]->rsRecordSet);
    $obRegra->setRecordSetClassificacao($obImportacaoCatalogo->arArquivos[4]->rsRecordSet);
    $obRegra->setRecordSetItem($obImportacaoCatalogo->arArquivos[5]->rsRecordSet);
if ($_FILES['stArquivoAtributo']['size'] != 0) {
    $obRegra->setRecordSetAtributo($obImportacaoCatalogo->arArquivos[6]->rsRecordSet);
    if ($_FILES['stArquivoItemAtributos']['size'] != 0) {
        $obRegra->setRecordSetItemAtributo($obImportacaoCatalogo->arArquivos[7]->rsRecordSet);
    }
}
    $obErro =  $obRegra->incluir();
    //apaga os arquivos que foram gravados no servidor
    foreach ($_FILES as $chave) {
        @unlink($stCaminhoUpload.$chave['name']);
    }
    @unlink($stCaminhoUpload."tipoItem.csv");
    @unlink($stCaminhoUpload."unidadeMedida.csv");
    @unlink($stCaminhoUpload."tipoAtributo.csv");
    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgForm,"Importação de Catálogos Concluída com sucesso !","generica","incluir", Sessao::getId(), "../");
    } else {
        SistemaLegado::alertaAviso($pgForm,"Erro na Importação de Catálogos! ( ".$obErro->getDescricao()." !)","generica","erro", Sessao::getId(), "../");
    }
} else {
    SistemaLegado::alertaAviso($pgForm,"Erro na Importação de Catálogos! (Verifique se o nome da Descrição do Catálogo do Form é o nome da Descrição do Catálogo do arquivo CSV são iguais !)","generica","erro", Sessao::getId(), "../");
}
