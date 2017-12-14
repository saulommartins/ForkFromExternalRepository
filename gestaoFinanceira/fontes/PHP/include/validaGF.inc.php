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
* Data de Criação: 12/12/2005

* @author Desenvolvedor: Lucas Leusin Oaigen
* @author Documentor: Lucas Leusin Oaigen

* @package framework
* @subpackage componentes

Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.6  2006/12/22 17:05:51  rodrigo
Correção no bloqueio das rotinas do bando cd dados apos a virada de ano.

Revision 1.5  2006/12/21 15:52:19  rodrigo
Bloqueio das rotinas do bando cd dados apos a virada de ano.

Revision 1.4  2006/07/05 20:45:35  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );


include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
$obTEntidade = new TOrcamentoEntidade;
$obTEntidade->setDado('exercicio', Sessao::getExercicio());
$obTEntidade->setDado('valor', 't');
$obErro = $obTEntidade->verificaEntidadeRestos( $rsRecordSet, $boTransacao );

if ($rsRecordSet->getNumLinhas() > 0) {
    SistemaLegado::exibeAlertaTopo('Há entidades que já processaram seus Restos a Pagar para o ano de '.Sessao::getExercicio().'. Portanto, é possível que nem todas estejam disponíveis para seleção.');
}
