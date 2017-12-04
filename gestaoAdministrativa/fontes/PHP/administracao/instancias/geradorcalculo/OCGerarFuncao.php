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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RBiblioteca.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RModulo.class.php"     );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastro.class.php"   );

$obRBiblioteca = Sessao::read('obRBiblioteca');

switch ($_POST["stCtrl"]) {
    case "buscaCadastro":
         $stJs .= "limpaSelect(f.inCodCadastro ,1); \n";
         $stJs .= "limpaSelect(f.arAtributoSemFunc, 0); \n";
         $stJs .= "limpaSelect(f.arAtributoComFunc, 0); \n";
         $stJs .= "limpaSelect(f.inCodBiblioteca, 1);\n";
         if( $_POST["inCodModulo"] )
         $obRBiblioteca->roRModulo->setCodModulo( $_POST["inCodModulo"] );
         if ($_POST["inCodModulo"]) {
             //$obRCadastro = new RCadastro( $obRBiblioteca->roRModulo );
             $obRBiblioteca->roRModulo->addCadastro();
             $obErro = $obRBiblioteca->roRModulo->roRCadastro->listarCadastro( $rsCadastro );
             if ( !$obErro->ocorreu() ) {
                 $i = 0;
                 while ( !$rsCadastro->eof() ) {
                     $stJs .= "f.inCodCadastro.options[".++$i."] = new Option('".$rsCadastro->getCampo("nom_cadastro")."','".$rsCadastro->getCampo("cod_cadastro")."');\n";
                     $rsCadastro->proximo();
                 }
                 $obErro = $obRBiblioteca->listarBibliotecasPorModulo( $rsBiblioteca );
                 if ( !$obErro->ocorreu() ) {
                     $i = 0;
                     while ( !$rsBiblioteca->eof() ) {
                         $stJs .= "f.inCodBiblioteca.options[".++$i."] = new Option('".$rsBiblioteca->getCampo("nom_biblioteca")."','".$rsBiblioteca->getCampo("cod_biblioteca")."');\n";
                         $rsBiblioteca->proximo();
                     }
                 }
             }
             if ( !$obErro->ocorreu() ) {
                  $stJs .= " erro = true;\n";
                  $stJs .= " mensagem = '".$obErro->getDescricao()."';\n";
             }
         }
         SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "buscaAtributos":
         $stJs .= "limpaSelect(f.arAtributoSemFunc, 0); \n";
         $stJs .= "limpaSelect(f.arAtributoComFunc, 0); \n";
         if ($_POST["inCodCadastro"]) {
             $obRBiblioteca->roRModulo->roRCadastro->setCodCadastro( $_POST["inCodCadastro"] );
             $obErro = $obRBiblioteca->roRModulo->roRCadastro->listarAtributosSemFuncao( $rsSemFuncao );
             if ( !$obErro->ocorreu() ) {
                 $obErro = $obRBiblioteca->roRModulo->roRCadastro->listarAtributosComFuncao( $rsComFuncao );
                 if ( !$obErro->ocorreu() ) {
                     $i = 0;

                     while ( !$rsSemFuncao->eof() ) {
                         $stJs .= "f.arAtributoSemFunc.options[".$i++."] = new Option('".$rsSemFuncao->getCampo("nom_atributo")."','".$rsSemFuncao->getCampo("cod_atributo")."');\n";
                         $rsSemFuncao->proximo();
                     }
                     $i = 0;
                     while ( !$rsComFuncao->eof() ) {
                         $stJs .= "f.arAtributoComFunc.options[".$i++."] = new Option('".$rsComFuncao->getCampo("nom_atributo")."','".$rsComFuncao->getCampo("cod_atributo")."');\n";
                         $rsComFuncao->proximo();
                     }
                 }
             }
            if ( $obErro->ocorreu() ) {
                $stJs .= " erro = true;\n";
                $stJs .= " mensagem = '".$obErro->getDescricao()."';\n";
            }
         }
         SistemaLegado::executaFrameOculto( $stJs );
    break;
}

Sessao::write('obRBiblioteca', $obRBiblioteca);

?>
