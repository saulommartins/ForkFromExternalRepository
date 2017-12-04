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
    * Arquivo de popup de busca de CGM
    * Data de Criação: 19/09/2006

    * @author Analista: Gelson Gonçalves
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage

    $Revision: 16257 $
    $Name$
    $Author: andre.almeida $
    $Date: 2006-10-02 14:56:14 -0300 (Seg, 02 Out 2006) $

    * Casos de uso: uc-01.01.00
*/

include_once ( CLA_SELECT );

class ISelectUnidadeMedida extends Select
{
    public function ISelectUnidadeMedida($stCodGrandeza = '')
    {
        parent::Select();

        include_once ( CAM_GA_ADM_NEGOCIO."RUnidadeMedida.class.php" );
        $obRUnidadeMedida = new RUnidadeMedida;
        if ($stCodGrandeza != '') {
            $obRUnidadeMedida->obRGrandeza->setCodGrandeza($stCodGrandeza);
        }
        $obRUnidadeMedida->listar($rsUnidadeMedida, " cod_unidade ");
        $rsUnidadeMedida->setPrimeiroElemento();
        array_shift($rsUnidadeMedida->arElementos);
        $rsUnidadeMedida->setNumLinhas($rsUnidadeMedida->getNumLinhas()-1);
        $rsUnidadeMedida->ordena("nom_unidade",'ASC',SORT_STRING);

        $this->setRotulo            ( "Unidade Medida"                         );
        $this->setTitle             ( "Selecione a unidade de medida do item." );
        $this->setName              ( "inCodUnidadeMedida"                     );
        $this->setNull              ( true                                     );
        $this->addOption            ( "","Selecione"                           );
        $this->setCampoID           ( "[cod_unidade]-[cod_grandeza]"           );
        $this->setCampoDesc         ("nom_unidade"                             );
        $this->preencheCombo        ( $rsUnidadeMedida                         );
        $this->setValue             ( isset($inCodAlmoxarifadoPadrao) ? $inCodAlmoxarifadoPadrao : "" );
    }
}
?>
