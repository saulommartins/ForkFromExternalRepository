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

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Revision: 16257 $
    $Name$
    $Author: andre.almeida $
    $Date: 2006-10-02 14:56:14 -0300 (Seg, 02 Out 2006) $

*/

include_once ( CLA_SELECT );

class ISelectCategoriaHabilitacao extends Select
{
    public function ISelectCategoriaHabilitacao($inCodigo = '')
    {
        parent::Select();

        include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaCategoriaHabilitacao.class.php" );
        $obTMapeamento = new TFrotaCategoriaHabilitacao;
        $obTMapeamento->recuperaTodos($rsRecordSet);

        $rsRecordSet->ordena("nom_categoria",ASC,SORT_STRING);

        $this->setRotulo            ( "Categoria de habilitação"                         );
        $this->setTitle             ( "Selecione a categoria nacional de habilitação." );
        $this->setName              ( "inCodCategoriaHabilitacao"                     );
        $this->setNull              ( true                                     );
        $this->addOption            ( "","Selecione"                           );
        $this->setCampoID           ( "cod_categoria"           );
        $this->setCampoDesc         ("nom_categoria"                             );
        $this->preencheCombo        ( $rsRecordSet                         );
        $this->setValue             ( $inCodigo                 );
    }
}
?>
