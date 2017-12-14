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
<?
/**
    * Data de Criação: 24/04/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: JSRelatorioContabil.js 62331 2015-04-24 14:39:45Z michel $

    * Casos de uso: uc-03.01.30
*/
?>
<script type="text/javascript" >
	function validaCampos(){
		if (Valida()) {
			var Erro = false;
			if (document.getElementById('stDataInicialIncorporacao') ) {
				if (document.getElementById('stDataInicialIncorporacao').value!='') {
					stDtInicial = document.getElementById('stDataInicialIncorporacao').value;
					stDtInicial = stDtInicial.split("/");

					if (stDtInicial[2]>document.getElementById('stExercicio').value) {
						document.getElementById('stExercicio').focus();
						alertaAviso('@Exercício da Periodicidade deve ser menor ou igual ao campo Exercício.','form','erro','<?=Sessao::getId();?>');
						Erro = true;
					}
				}
			}
			
			if (!Erro) {
				if (document.getElementById('stDataFinalIncorporacao') ) {
					if (document.getElementById('stDataFinalIncorporacao').value!='') {
						stDtFinal = document.getElementById('stDataFinalIncorporacao').value;
						stDtFinal = stDtFinal.split("/");
	
						if (stDtFinal[2]>document.getElementById('stExercicio').value) {
							document.getElementById('stExercicio').focus();
							alertaAviso('@Exercício da Periodicidade deve ser menor ou igual ao campo Exercício.','form','erro','<?=Sessao::getId();?>');
							Erro = true;
						}
					}
				}
			}
			
			if (!Erro) {
				Salvar();
			}
		}
	}
</script>
