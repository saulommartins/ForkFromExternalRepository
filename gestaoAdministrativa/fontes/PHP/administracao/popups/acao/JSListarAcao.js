<script type="text/javascript">
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
</script>
<?php
/**
* Arquivo de instância para manutenção de normas
* Data de Criação: 06/09/2005


* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15641 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 13:25:02 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.03.93
*/
?>
<script type='text/javascript'>
function retornaAcao(inCodigoAcao, stNomeAcao ){
    window.opener.parent.frames['telaPrincipal'].document.<?=$stNomeForm?>.<?=$stCampoNum?>.value = inCodigoAcao;
<?if($stCampoNom){?>
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$stCampoNom?>').innerHTML = stNomeAcao;
<?}?>
    window.close();
}


function retornaAcaoOculto(inCodigoAcao, stNomeAcao ){
    var d = window.parent.frames["telaPrincipal"].document;
    d.<?=$stNomeForm?>.<?=$stCampoNum?>.value = inCodigoAcao;
<?if($stCampoNom){?>
    d.getElementById('<?=$stCampoNom?>').innerHTML = stNomeAcao;
<?}?>
    window.close();
}
</script>