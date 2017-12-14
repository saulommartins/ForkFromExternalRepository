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
    * Página de Formulário Oculto Concurso
    * Data de Criação   : 28/06/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Cristiano Brasil Sperb

    * @ignore

    * $Id: OCManterNatureza.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.08

*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"       );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$obRCEMNatureza = new RCEMNaturezaJuridica;
$obErro = new Erro;

switch ($stCtrl) {
    case "validaDigitoVerificador":
            $arCodigoNatureza = explode("-",$_REQUEST["inCodigoNatureza"]);
            $base = ( $arCodigoNatureza[0] );
            $s1=0;
            for ($i=0; $i<3 ;$i++) {
                $s1 += ($base[$i])* (4-$i);
            }
            $r1 = $s1 % 11;
            if ($r1 < 2) {
                $d1=0;
            } else {
                $d1= (11-$r1);
            }

            if ($arCodigoNatureza[1] != $d1) {
                $obErro->setDescricao ("O dígito verificador (".$arCodigoNatureza[1].") é inválido para o código (".$base.").");
                $js = "alertaAviso('".urlencode($obErro->getDescricao())."','frm','erro','".Sessao::getId()."');\n";
                $js .="f.inCodigoNatureza.value = '' ;\n" ;
                $js .="f.inCodigoNatureza.focus();";
            } else {
             $js .="f.inCodigoNatureza.value = '".$arCodigoNatureza[0]."-".$arCodigoNatureza[1]."' \n";
            }
            sistemaLegado::executaFrameOculto($js);
    break;
}
