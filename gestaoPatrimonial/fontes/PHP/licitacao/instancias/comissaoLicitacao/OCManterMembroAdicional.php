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
    * Oculto do Formulário para consulta de MEmbros adicionais de licitacao
    * Data de Criação   : 22/05/2009

    * @author Analista      Gelson Gonçalves
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCManterMembroAdicional.php 62332 2015-04-24 14:43:32Z jean $
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoMembroAdicional.class.php");
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoNaturezaCargo.class.php'                              );


//Define o nome dos arquivos PHP
$stPrograma = "ManterMembroAdicional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];
$arMembrosAdicionais = Sessao::read('arMembrosAdicionais');

switch ($stCtrl) {
case 'montaAcoes':
    $stFiltro  = " AND MA.cod_licitacao  = ".$_REQUEST['cod_licitacao'];
    $stFiltro .= " AND MA.cod_modalidade = ".$_REQUEST['cod_modalidade'];
    $stOrder   = " ORDER BY cgm.nom_cgm";

    $obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional;
    $obTLicitacaoMembroAdicional->setDado('exercicio', $_REQUEST['exercicio']);
    $obTLicitacaoMembroAdicional->recuperaMembroAdicional($rsMembrosAdicionais, $stFiltro, $stOrder);

    //Validar se existe mais o mesmo membro para varias licitacoes
    if (isset($arMembrosAdicionais) ) {
        foreach ($rsMembrosAdicionais->getElementos() as $dados) {
            array_push($arMembrosAdicionais, $dados);     
        } 
    }else{
        $arMembrosAdicionais = $rsMembrosAdicionais->getElementos();
    }
    
    Sessao::write('arMembrosAdicionais',$arMembrosAdicionais);    

    //Dados
    $obTxtCargo  = new TextBox;     
    $obTxtCargo->setRotulo      ( "Cargo do membro"                           );
    $obTxtCargo->setTitle       ( "Informe o cargo do membro."                );
    $obTxtCargo->setName        ( "stCargoMembro_[numcgm]_[cod_licitacao]"    );
    $obTxtCargo->setValue       ( "[cargo]"                                   );
    $obTxtCargo->setMaxLength   ( 50                                          );
    $obTxtCargo->setSize        ( 60                                          );
    
    $obTNaturezaCargo = new TLicitacaoNaturezaCargo;
    $obTNaturezaCargo->recuperaTodos( $rsNaturezaCargo, ' where codigo <> 0' );

    $obCmbNaturezaCargo = new Select;
    $obCmbNaturezaCargo->setRotulo    ( 'Natureza do Cargo'                 );
    $obCmbNaturezaCargo->setTitle     ( 'Selecione a Natureza do Cargo.'    );
    $obCmbNaturezaCargo->setName      ( "inNaturezaCargo_[numcgm]_[cod_licitacao]" );
    $obCmbNaturezaCargo->setStyle     ( "width: 360px"                      );
    $obCmbNaturezaCargo->setValue     ( '[natureza_cargo]'                  );
    $obCmbNaturezaCargo->addOption    ( "","Selecione"                      );
    $obCmbNaturezaCargo->setCampoId   ( "[codigo]"                          );
    $obCmbNaturezaCargo->setCampoDesc ( "[codigo] - [descricao]"            );
    $obCmbNaturezaCargo->preencheCombo( $rsNaturezaCargo                    );

    //Instancia uma Table para demonstrar as ações
    $obTable = new Table();
    $obTable->setRecordset ($rsMembrosAdicionais);
    $obTable->setSummary   ('Lista de Membros adicionais');

    $obTable->Head->addCabecalho('Nome',45);
    $obTable->Head->addCabecalho('Cargo ',25);
    $obTable->Head->addCabecalho('Natureza do Cargo',25);

    $obTable->Body->addCampo('nom_cgm','E','nom_cgm');
    $obTable->Body->addComponente($obTxtCargo);
    $obTable->Body->addComponente($obCmbNaturezaCargo);
    $obTable->montaHTML();
 
    echo $obTable->getHTML();
    break;
}

?>
