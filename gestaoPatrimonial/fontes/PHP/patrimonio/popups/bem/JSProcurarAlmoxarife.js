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
* Arquivo de popup para manutenção de usuários
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 25675 $
$Name$
$Author: hboaventura $
$Date: 2007-09-27 09:57:24 -0300 (Qui, 27 Set 2007) $

Casos de uso: uc-03.03.02
*/

/*
$Log$
Revision 1.1  2007/09/27 12:56:43  hboaventura
adicionando arquivos

Revision 1.5  2006/07/06 14:05:39  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:10:10  diego


*/
?>

<script type="text/javascript">

function insere(inNumCGM,stNomCGM,stUsername){
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodCGMAlmoxarife.value         = inNumCGM;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('stNomCGM').innerHTML = stNomCGM;
    window.close();
}
</script>
