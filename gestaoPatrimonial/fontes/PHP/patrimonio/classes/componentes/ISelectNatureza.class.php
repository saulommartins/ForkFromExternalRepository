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
* Arquivo de select de natureza
* Data de Criação: 25/05/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Fernando Zank Correa Evangelista

* @package URBEM
* @subpackage

$Revision: 28706 $
$Name$
$Author: luiz $
$Date: 2008-03-24 16:17:06 -0300 (Seg, 24 Mar 2008) $

* Casos de uso: uc-00.00.00
*/

/*
$Log$
Revision 1.4  2007/09/18 15:36:20  hboaventura
Adicionando ao repositório

Revision 1.3  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:27  diego

*/

include_once ( CLA_SELECT );

class  ISelectNatureza extends Select
{

    public $obForm;
    public $obTPatrimonioNatureza;

    public function ISelectNatureza($obForm)
    {
        include_once (TPAT."TPatrimonioNatureza.class.php");
        $this->obTPatrimonioNatureza =  new TPatrimonioNatureza();
        parent::Select();

        $this->obForm = $obForm;
        $this->setName     ('inCodNatureza');
        $this->setId       ('inCodNatureza');
        $this->setRotulo   ( "Natureza" );
        $this->setStyle    ( "width: 270px;"              );
        $this->setTitle    ( "Informe a natureza." );
        $this->setNull     ( false );
        $this->addOption   ( "", "Selecione"                    );
        $this->setCampoId  ("cod_natureza");
        $this->setCampoDesc("[cod_natureza] - [nom_natureza]");

    }
    public function montaHTML()
    {

        $this->obTPatrimonioNatureza->recuperaTodos($rsNatureza,'',' ORDER BY nom_natureza');
        $this->preencheCombo($rsNatureza);
        parent::montaHTML();
    }
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this );
    }
}
?>
