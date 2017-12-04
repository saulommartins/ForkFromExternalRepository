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
    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 27/02/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Revision: 12551 $
    $Name$
    $Author: leandro.zis $
    $Date: 2006-07-12 16:24:08 -0300 (Qua, 12 Jul 2006) $

    * Casos de uso: uc-03.03.06
*/

include_once ( CLA_OBJETO );

class  IMontaLabelItemUnidade extends Objeto
{
    public $obLabelCatalogoItem;
    public $obILabelUnidadeMedida;

    public function IMontaLabelItemUnidade()
    {
        parent::Objeto();
        $this->obLabelCatalogoItem = new Label();
        $this->obLabelCatalogoItem->setRotulo('Item');
        $this->obLabelCatalogoItem->setId    ('stCatalogoItem'  );

        include_once(CAM_GA_ADM_COMPONENTES.'ILabelUnidadeMedida.class.php');
        $this->obILabelUnidadeMedida = new ILabelUnidadeMedida;

    }

    public function setCodItem($inCodItem)
    {
        $this->inCodItem = $inCodItem;
    }

    public function geraFormulario(&$obFormulario)
    {
        if ($this->inCodItem) {
            $rsCatalogoItem = new RecordSet;
            include_once(CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCatalogoItem.class.php');
            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
            $obTAlmoxarifadoCatalogoItem->setDado('cod_item', $this->inCodItem);
            $obTAlmoxarifadoCatalogoItem->recuperaPorChave($rsCatalogoItem);
            $this->obLabelCatalogoItem->setValue($rsCatalogoItem->getCampo('cod_item').' - '.$rsCatalogoItem->getCampo('descricao'));
            $this->obILabelUnidadeMedida->setCodUnidade($rsCatalogoItem->getCampo('cod_unidade'));
            $this->obILabelUnidadeMedida->setCodGrandeza($rsCatalogoItem->getCampo('cod_grandeza'));
        }
        $obFormulario->addComponente( $this->obLabelCatalogoItem  );
        $obFormulario->addComponente( $this->obILabelUnidadeMedida );
    }
}
?>
