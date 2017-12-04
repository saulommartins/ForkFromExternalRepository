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
* Arquivo de select de situacao
* Data de Criação: 15/01/2009

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

* @package URBEM
* @subpackage

*/

include_once ( CLA_SELECT );

class  ISelectSituacaoBem extends Select
{
    public function ISelectSituacaoBem()
    {
        parent::Select();

        $this->setName     ('inCodSituacao');
        $this->setId       ('inCodSituacao');
        $this->setRotulo   ( "Situação" );
        $this->setStyle    ( "width: 150px;"              );
        $this->setTitle    ( "Informe a situação." );
        $this->setNull     ( false );
        $this->addOption   ( "", "Selecione"                    );
        $this->setCampoId  ("cod_situacao");
        $this->setCampoDesc("[cod_situacao]-[nom_situacao]");
    }
    public function montaHTML()
    {
        include_once (TPAT."TPatrimonioSituacaoBem.class.php");
        $rsRecordSet   = new RecordSet();
        $obTMapeamento = new TPatrimonioSituacaoBem();
        $obTMapeamento->recuperaTodos($rsRecordSet,'',' ORDER BY cod_situacao');
        $this->preencheCombo($rsRecordSet);
        parent::montaHTML();
        unset($rsRecordSet);
        $rsRecordSet = null;
    }
}
?>
