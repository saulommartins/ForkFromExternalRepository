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
 * Página de JavaScript do componente IPopUpConta
 * Data de Criação: 07/09/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.mendes>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.03
 */
?>
<script>
function insereCodConta(nom, cod)
{
    var sNom;
    var sCod;

    sNom = nom;
    sCod = cod;

    window.opener.parent.frames['telaPrincipal'].document.getElementById(<?php echo "'".$_REQUEST["campoNom"]."'";?>).innerHTML = sNom;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?php echo $_REQUEST["campoNom"];?>.innerHTML = sNom;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('inCodConta').value = sCod;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?php echo $_REQUEST["campoNum"];?>.value = sCod;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?php echo $_REQUEST["campoNum"];?>.focus();
    window.close();
}
</script>
