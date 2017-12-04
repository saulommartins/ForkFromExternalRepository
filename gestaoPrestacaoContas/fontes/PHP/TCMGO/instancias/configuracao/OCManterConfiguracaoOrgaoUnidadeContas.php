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
    * Página de Formulario de Ajustes Gerais Exportacao - TCE-RS
    * Data de Criação   : 11/07/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-17 11:32:12 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.08.15
*/

/*
$Log$
Revision 1.1  2006/07/17 14:30:48  cako
Bug #6013#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );

//$boTransacao = new Transacao;

switch ($_REQUEST['stCtrl']) {
    case 'verificaEntidades':
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->recuperaTodos($rsRecord, " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 AND parametro ilike '%cod_entidade%'");//, "", $boTransacao);

        foreach ($rsRecord->getElementos() as $i => $val) {
            $obROrcamentoEntidade = new ROrcamentoEntidade;
            $obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
            $obROrcamentoEntidade->setCodigoEntidade($val['valor']);
            $obROrcamentoEntidade->consultar($rsLista);

            if ($rsLista->getNumLinhas() < 1) {
                switch ($val['parametro']) {
                    case 'cod_entidade_prefeitura':
                        $stJs .= " jq('#inCodExecutivo').attr('disabled','disabled'); ";
                    break;

                    case 'cod_entidade_camara':
                        $stJs .= " jq('#inCodLegislativo').attr('disabled','disabled'); ";
                    break;

                    case 'cod_entidade_rpps':
                        $stJs .= " jq('#inCodRPPS').attr('disabled','disabled'); ";
                    break;

                    case 'cod_entidade_consorcio':
                        $stJs .= " jq('#inCodOutros').attr('disabled','disabled'); ";
                    break;
                }
            }
        }

    break;
}

if ($stJs) {
    echo $stJs;
}
