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
* Arquivo de select da marca
* Data de Criação: 14/07/2006

* @author Analista: Gelson Wolowski Gonçalves
* @author Desenvolvedor: Gelson Wolowski Gonçalves

* @package URBEM
* @subpackage

$Revision: 27758 $
$Name$
$Author: hboaventura $
$Date: 2008-01-28 07:15:55 -0200 (Seg, 28 Jan 2008) $

* Casos de uso: uc-03.02.03
*/

/*
$Log$
Revision 1.1  2006/07/14 13:25:36  gelson
Adicionando ao repositório.

*/

include_once ( CLA_SELECT );

class  ISelectMarcaVeiculo extends Select
{

    public $obForm;
    public $obTFrotaMarca;

    public function ISelectMarcaVeiculo(&$obForm)
    {
        include_once (TFRO."TFrotaMarca.class.php");
        $this->obTFrotaMarca = new TFrotaMarca();
        parent::Select();

        $this->obForm = &$obForm;
        $this->setName     ('inCodMarca');
        $this->setId       ('inCodMarca');
        $this->setRotulo   ( "Marca" );
        $this->setStyle    ( "width: 270px;"              );
        $this->setTitle    ( "Selecione a descrição da marca." );
        $this->setNull     ( false );
        $this->addOption   ( "", "Selecione"                    );
        $this->setCampoId  ("cod_marca");
        $this->setCampoDesc("nom_marca");

    }
    public function montaHTML()
    {

        $this->obTFrotaMarca->recuperaTodos($rsMarca,'',' ORDER BY nom_marca');
        $this->preencheCombo($rsMarca);
        parent::montaHTML();
    }
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this );
    }
}
?>
