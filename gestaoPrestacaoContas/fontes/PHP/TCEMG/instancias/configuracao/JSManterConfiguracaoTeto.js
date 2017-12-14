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
    *                        
    **********************************************************************************                                                           
*/

/**
  * Arquivo JavaScript utilizado no Form de Configurar Teto Remuneratório
  * Data de Criação: 01/04/2016

  * @author Analista:      Dagiane Vieira
  * @author Desenvolvedor: Michel Teixeira
  *
  * @ignore
  * $Id: JSManterConfiguracaoTeto.js 64799 2016-04-01 18:32:14Z michel $
  *
*/

function modificaDado(tipoBusca, inId){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+inId;
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

</script>