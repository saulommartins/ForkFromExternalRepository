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
    * Página de Filtro para Relatório LRF
    * Data de Criação   : 24/05/2005


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso  uc-02.05.03
                    uc-02.05.04
                    uc-02.05.05
                    uc-02.05.06
                    uc-02.05.07
                    uc-02.05.08
                    uc-02.05.10     
                    uc-02.01.35

    * @ignore
*/

/*
$Log$
Revision 1.6  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.5  2006/08/25 17:50:22  fernando
Bug #6773#

Revision 1.4  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script>
function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '../../../../../../gestaoFinanceira/fontes/PHP/LRF/instancias/tceRS/OCModelosExecutivo.php?stCtrl='+variavel+'&<?=Sessao::getId();?>'; document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

</script>
                                
