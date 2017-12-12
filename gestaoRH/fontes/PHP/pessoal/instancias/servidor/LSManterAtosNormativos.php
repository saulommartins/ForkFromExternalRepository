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
* Página de Listagem de Organograma
* Data de Criação   : 25/08/2004

* @author Analista: ???
* @author Desenvolvedor: Diego Barbosa Victoria

* @ignore

$Revision: 30547 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$rsLista = new RecordSet;

$obRPessoalServidor->obRPessoalContratoServidor->obRPessoalAtoNormativo->obTPessoalAtoNormativo->recuperaTodos( $rsLista );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->setMostraPaginacao( false    );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de norma" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Norma");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

if ($_REQUEST["stAcao"] == "alterar") {
    $obChkAtoNormativo = new CheckBox;
    $obChkAtoNormativo->setName           ( "inAtoNormativo"   );
    $obChkAtoNormativo->setValue          ( 'cod_ato_normativo' );
    $obChkAtoNormativo->setChecked        ( ($inAtoNormativo == 'cod_ato_normativo') );
} else {
    $obChkAtoNormativo = new CheckBox;
    $obChkAtoNormativo->setName           ( "cod_ato_normativo"   );
    $obChkAtoNormativo->setValue          ( 'true' );
    $obChkAtoNormativo->setChecked        ( ($inAtoNormativo == true) );

}

$obRPessoalServidor->obRPessoalContratoServidor->obRNorma->obRTipoNorma->obTTipoNorma->recuperaTodos( $rsTipoNorma );
$obCmbCodTipoNorma = new Select;
$obCmbCodTipoNorma->setName                  ( "inCodTipoNorma"             );
$obCmbCodTipoNorma->setValue                 ( $inCodTipoNorma              );
$obCmbCodTipoNorma->setTitle                 ( "Selecione o tipo de norma."  );
$obCmbCodTipoNorma->setNull                  ( true                         );
$obCmbCodTipoNorma->setCampoId               ( "[cod_tipo_norma]"           );
$obCmbCodTipoNorma->setCampoDesc             ( "nom_tipo_norma"             );
$obCmbCodTipoNorma->addOption                ( "", "Selecione"              );
$obCmbCodTipoNorma->preencheCombo            ( $rsTipoNorma                 );
$obCmbCodTipoNorma->setStyle                 ( "width: 250px"               );
$obCmbCodTipoNorma->obEvento->setOnChange    ( "preencheNorma();"      );

$obCmbCodNorma = new Select;
$obCmbCodNorma->setName                  ( "inCodNorma"             );
$obCmbCodNorma->setValue                 ( $inCodNorma              );
$obCmbCodNorma->setTitle                 ( "Selecione a norma."  );
$obCmbCodNorma->setNull                  ( true                         );
$obCmbCodNorma->setCampoId               ( "[cod_norma]"           );
$obCmbCodNorma->setCampoDesc             ( "nom_norma"             );
$obCmbCodNorma->setStyle                 ( "width: 250px"               );
$obCmbCodNorma->preencheCombo            ( $rsTipoNorma                 );
$obCmbCodNorma->addOption                ( "", "Selecione"              );

$obLista->addDadoComponente( $obChkAtoNormativo );
$obLista->ultimoDado->setCampo( "apresentada" );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_ato_normativo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDadoComponente( $obCmbCodTipoNorma );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();

$obLista->addDadoComponente( $obCmbCodNorma );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();

$obLista->montaHTML();
$stHtml = $obLista->getHTML();
$stHtml = str_replace("\n","",$stHtml);
$stHtml = str_replace("  ","",$stHtml);
$stHtml = str_replace("'","\\'",$stHtml);

// preenche a lista com innerHTML
$stJs .= "d.getElementById('spnAtoNormativo').innerHTML = '".$stHtml."';";

?>
