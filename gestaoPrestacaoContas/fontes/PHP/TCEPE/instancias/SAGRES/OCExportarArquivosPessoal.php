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
  * Página Oculta para o formulário FLExportarArquivosPessoal.php
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCExportarArquivosPessoal.php 60426 2014-10-21 11:54:26Z gelson $
  * $Date: 2014-10-21 09:54:26 -0200 (Tue, 21 Oct 2014) $
  * $Author: gelson $
  * $Rev: 60426 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaListaArquivos( $inCompetencia )
{
    $arArquivos = array();
    
    $arArquivos[] = array( "nome"    => "Servidores Pensionistas",
                           "arquivo" => "ServidoresPensionistas"
                       );
    $arArquivos[] = array( "nome"    => "Cargos",
                           "arquivo" => "Cargos"
                       );
    $arArquivos[] = array( "nome"    => "Classe Nível Faixa",
                           "arquivo" => "ClasseNivelFaixa"
                       );
    
    if ( $inCompetencia != 14 ) {
        $arArquivos[] = array( "nome"    => "Histórico Funcional",
                               "arquivo" => "HistoricoFuncional"
                           );
        $arArquivos[] = array( "nome"    => "Folha Pagamento",
                               "arquivo" => "FolhaPagamento"
                           );
        $arArquivos[] = array( "nome"    => "Código Vantagem Desconto",
                               "arquivo" => "CodigoVantagemDesconto"
                           );
        $arArquivos[] = array( "nome"    => "Vantagem Desconto",
                               "arquivo" => "VantagemDesconto"
                           );
        $arArquivos[] = array( "nome"    => "Histórico Pessoal",
                               "arquivo" => "HistoricoPessoal"
                           );
        $arArquivos[] = array( "nome"    => "Dependentes",
                               "arquivo" => "Dependentes"
                           );
        $arArquivos[] = array( "nome"    => "Lotação",
                               "arquivo" => "Lotacao"
                           );
    }
    return $arArquivos;

}


function montaPreencheArquivos($inCodCompetencia)
{
    $arArquivos = array();
    $rsArquivos = new RecordSet;
    $rsArquivos->preenche(montaListaArquivos($inCodCompetencia));
    $rsArquivos->ordena('nome', 'ASC', SORT_STRING);
    // Define SELECT multiplo para os arquivos
    $obCmbArquivos = new SelectMultiplo();
    $obCmbArquivos->setName( 'arArquivos' );
    $obCmbArquivos->setRotulo( 'Arquivos' );
    $obCmbArquivos->setTitle( '' );
    $obCmbArquivos->setNull( false );
    
    // lista as entidades disponiveis
    $obCmbArquivos->SetNomeLista1( 'arArquivoDisponivel' );
    $obCmbArquivos->setCampoId1( 'arquivo' );
    $obCmbArquivos->setCampoDesc1( 'nome' );
    $obCmbArquivos->SetRecord1( $rsArquivos );
    
    // lista as entidades selecionados
    $obCmbArquivos->SetNomeLista2( 'arArquivos' );
    $obCmbArquivos->setCampoId2( 'arquivo' );
    $obCmbArquivos->setCampoDesc2( 'nome' );
    $obCmbArquivos->SetRecord2( new RecordSet );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbArquivos );
    $obFormulario->montaInnerHTML();
    
    $stHtml = $obFormulario->getHTML();
    $stJs .= "document.getElementById('obCmbArquivos').innerHTML = '".$stHtml."' ";
    
    
    return $stJs;
}

switch($_GET['stCtrl']){
    case "montaMultipleSelect":
        $inCodCompetencia = $_REQUEST["inCodCompetencia"];
        if( $inCodCompetencia != '' ){
            $stJs = montaPreencheArquivos($inCodCompetencia);
        }else{
            $stJs = "document.getElementById('obCmbArquivos').innerHTML = ''"; 
        }
        break;
}

if ($stJs) {
   echo $stJs;
}
?>