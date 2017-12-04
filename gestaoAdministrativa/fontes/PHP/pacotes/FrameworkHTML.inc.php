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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkErro.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkRecordset.inc.php';

//INTERFACE
include_once ( CLA_ABA );
include_once ( CLA_ACAO );
include_once ( CLA_CELULA );
include_once ( CLA_CABECALHO );
include_once ( CLA_DADO );
include_once ( CLA_EVENTO );
include_once ( CLA_JAVASCRIPT );
include_once ( CLA_LINHA );
include_once ( CLA_TABELA );
include_once ( CLA_FORMULARIO );
include_once ( CLA_FORMULARIO_ABAS );
include_once ( CLA_DATAGRID );
include_once ( CLA_PAGINACAO );
include_once ( CLA_LISTA );
//MASCARA
include_once ( CLA_MASCARA );
include_once ( CLA_MASCARA_CEP );
include_once ( CLA_MASCARA_CNPJ );
include_once ( CLA_MASCARA_CPF );
include_once ( CLA_MASCARA_DATA );
//COMPONENTES
include_once ( CLA_COMPONENTE_BASE );
include_once ( CLA_COMPONENTE );
include_once ( CLA_ARVORE );
include_once ( CLA_BUTTON );
include_once ( CLA_CALENDARIO );
include_once ( CLA_CHECKBOX );
include_once ( CLA_CHECKBOX_DINAMICO );
include_once ( CLA_FORM );
include_once ( CLA_FILEBOX );
include_once ( CLA_HIDDEN );
include_once ( CLA_IMG );
include_once ( CLA_IPOPUPCGM );
include_once ( CLA_LINK );
include_once ( CLA_OPTION );
include_once ( CLA_PROGRESSBAR );
include_once ( CLA_RADIO );
include_once ( CLA_RESET );
include_once ( CLA_SELECT );
include_once ( CLA_BIMESTRE );
include_once ( CLA_SELECT_MESES );
include_once ( CLA_SPAN );
include_once ( CLA_SUBMIT );
include_once ( CLA_TEXTAREA );
include_once ( CLA_TEXTBOX );
include_once ( CLA_TEXTBOX_SELECT );
include_once ( CLA_GERENCIA_SELECTS );
include_once ( CLA_SELECT_MULTIPLO );
include_once ( CLA_LABEL );
include_once ( CLA_MONTA_ATRIBUTOS );
include_once ( CLA_TIPO_BUSCA );
include_once ( CLA_APPLET );
include_once ( CLA_PASSWORD );
include_once ( CLA_IMAGE_BOX );

//TIPO
include_once ( CLA_BUSCA );
include_once ( CLA_BUSCAINNER );
include_once ( CLA_POPUP      );
include_once ( CLA_BUSCAINNERINTERVALO );
include_once ( CLA_CAMPOINNER );
include_once ( CLA_CANCELAR );
include_once ( CLA_CEP );
include_once ( CLA_CNPJ );
include_once ( CLA_CPF );
include_once ( CLA_DADO_COMPONENTE );
include_once ( CLA_DADO_TEXTBOX );
include_once ( CLA_DATA );
include_once ( CLA_PLACA_VEICULO);
include_once ( CLA_PERIODO );
include_once ( CLA_PERIODICIDADE );
include_once ( CLA_MES );
include_once ( CLA_EXERCICIO );
include_once ( CLA_QUANTIDADE );
include_once ( CLA_VALOR_TOTAL );
include_once ( CLA_VALOR_UNITARIO );
include_once ( CLA_HIDDENEVAL );
include_once ( CLA_HORA );
include_once ( CLA_LIMPAR );
include_once ( CLA_MOEDA );
include_once ( CLA_PORCENTAGEM );
include_once ( CLA_NUMERICO );
include_once ( CLA_INTEIRO );
include_once ( CLA_OK );
include_once ( CLA_SIMNAO );
include_once ( CLA_VOLTAR );
include_once ( CLA_POPUPEDIT );

//FRAME
include_once ( CLA_IFRAME );
?>
