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
    * Arquivo de componente
    * Data de Criação: 21/04/2009

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

class ISelectUF extends Select
{
    public function ISelectUF($inCodUF = '')
    {
        parent::Select();

        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
        $obTMapeamento = new TUF;
        $obTMapeamento->recuperaTodos($rsRecordSet);

        $rsRecordSet->ordena("nom_uf",ASC,SORT_STRING);

        $this->setRotulo            ( "Estado"                         );
        $this->setTitle             ( "Selecione o estado." );
        $this->setName              ( "inCodUF"                     );
        $this->setNull              ( true                                     );
        $this->addOption            ( "","Selecione"                           );
        $this->setCampoID           ( "cod_uf"           );
        $this->setCampoDesc         ("nom_uf"                             );
        $this->preencheCombo        ( $rsRecordSet                         );
        $this->setValue             ( $inCodUF                 );
    }
}
?>
