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
    * Arquivo de label de Unidade de Medida
    * Data de Criação: 28/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.01.00
*/

include_once ( CLA_LABEL );

class  ILabelUnidadeMedida extends Label
{
    public $inCodUnidade;
    public $inCodGrandeza;

    public function ILabelUnidadeMedida()
    {
        parent::Label();
        $this->setRotulo('Unidade de Medida');
    }

    public function setCodUnidade($inValor) { $this->inCodUnidade = $inValor ; }

    public function setCodGrandeza($inValor) { $this->inCodGrandeza = $inValor ; }

    public function montaHTML()
    {
        if ( !$this->getValue() ) {
            include_once(CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php");
            $obTUnidadeMedida = new TUnidadeMedida();
            $obTUnidadeMedida->setDado('cod_unidade', $this->inCodUnidade);
            $obTUnidadeMedida->setDado('cod_grandeza', $this->inCodGrandeza);
            $obTUnidadeMedida->recuperaPorChave($rsRecordSet);

            $this->setValue( $rsRecordSet->getCampo('nom_unidade') );
        }

        parent::montaHTML();
    }
}
?>
