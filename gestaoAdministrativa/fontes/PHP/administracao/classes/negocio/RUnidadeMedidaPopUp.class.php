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
 * Classe de negócio UnidadeMedida
 * Data de Criação: 26/08/2008

 * @author Analista: Heleno Menezes dos Santos
 * @author Desenvolvedor: Janilson Mendes P. da Silva

$Revision: $
$Name$
$Author:  $
$Date:  $

Casos de uso:
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CAM_GA_ADM_NEGOCIO."RUnidadeMedida.class.php");

class RUnidadeMedidaPopUp extends RUnidadeMedida
{
    public $CriterioSql = null;
    private $obTransacao;

    public function __construct()
    {
        parent::RUnidadeMedida();
    }

    protected function chamarMapeamento($mapeamento , $metodo , $criterio)
    {
        $where = " where ";
        if ($criterio) {
            $criterio = $where.$criterio;
        }
        $obRs = new RecordSet();
        $ob = $mapeamento;
        $ob->$metodo( $obRs,$criterio );

        return $obRs;
    }

    public function setCriterio($vlr)
    {
        $this->CriterioSql = $vlr;
    }

    public function getListarUnidade()
    {
        return $this->chamarMapeamento( parent::$this->obTUnidadeMedida,"recuperaTodos",$this->CriterioSql);
    }
}

final class VUnidadeMedidaPopUp
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function recuperarListaUnidade($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio( $stFiltro );
        }

        return $this->controller->getListarUnidade();
    }

    public function filtrosUnidadeMedida($arParametros)
    {
        if ($arParametros['stNomeUnidade'] != '') {
            $stFiltro[] = " nom_unidade ILIKE '%". $arParametros['stNomeUnidade'] ."%' ";
        }

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }

    public function preencheUnidade($arParam)
    {
            if ($arParam['inCodUnidade']) {

            $arCodUnidade = explode( '.', $arParam['inCodUnidade'] );
            $inCodUnidade = $arCodUnidade[0];
            $inCodGrandeza = $arCodUnidade[1];

            //$stFiltro = " WHERE cod_unidade = ".$inCodUnidade." AND cod_grandeza = ".$inCodGrandeza ;

            $obTUnidadeMedida = $this->controller->obTUnidadeMedida;
                $obTUnidadeMedida->setDado( "cod_unidade", $arCodUnidade[0] );
                $obTUnidadeMedida->setDado( "cod_grandeza", $arCodUnidade[1] );
                $obTUnidadeMedida->recuperaPorChave( $rsUnidade );

            if ( $rsUnidade->Eof() ) {
                    $stJs = "f.inCodUnidade.value ='';\n";
                    $stJs.= "f.inCodUnidade.focus();\n";
                    $stJs.= "d.getElementById('stUnidade').innerHTML = '&nbsp;';\n";
                    $stJs.= "alertaAviso('@Código informado não existe. (".$$arParam["inCodUnidade"].")','form','erro','".Sessao::getId()."');";
            } else {
                    $stJs = "d.getElementById('stUnidade').innerHTML = '".$rsUnidade->getCampo("simbolo")." (".$rsUnidade->getCampo("nom_unidade").")';\n";
                }
        } else {
            $stJs = "f.inCodUnidade.value ='';\n";
                $stJs .= "d.getElementById('stUnidade').innerHTML = '&nbsp;';\n";
                if ($arParam["inCodUnidade"] == '0') {
                     $stJs .= "alertaAviso('@Código informado não existe. (".$arParam["inCodUnidade"].")','form','erro','".Sessao::getId()."');";
                }
        }

        return $stJs;
    }
}
