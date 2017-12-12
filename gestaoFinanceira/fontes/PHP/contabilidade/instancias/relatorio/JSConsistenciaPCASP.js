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
    * Arquivo JS utilizado no relatório de Consistência PCASP
    * Data de Criação   : 25/09/2013


    * @author Analista:  Sergio Luiz dos Santos
    * @author Desenvolvedor: Jean Silva

    * @ignore
    
    * $Id: FLConsistenciaPCASP.php 52880 2012-08-28 19:15:58Z tonismar $
    
    * Casos de uso: uc-02.02.22
*/
?>
<script>
arDtInicio = document.frm.stDtInicio.value.split("/");
arDtTermino= document.frm.stDtTermino.value.split("/");
stValida   = ' @Campo Descrição inválido! ';

document.frm.boValidacao.value = 'arDtInicio = document.frm.stDtInicio.value.split("/"); arDtTermino = document.frm.stDtTermino.value.split("/");';

document.frm.boValidacao.value = document.frm.boValidacao.value + 'if( arDtInicio[2] != "<?=Sessao::getExercicio();?>" || arDtTermino[2] != "<?=Sessao::getExercicio();?>" ) {mensagem +="@As datas informadas devem pertencer ao mesmo exercício!"; erro = true;}';

document.frm.boValidacao.value = document.frm.boValidacao.value + 'if( document.frm.stDtInicio.value > document.frm.stDtTermino.value ) {mensagem +="@A data final deve ser maior ou igual a data inicial!"; erro = true;}';
</script>
