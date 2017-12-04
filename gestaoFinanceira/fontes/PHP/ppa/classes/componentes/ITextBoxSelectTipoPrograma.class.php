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
* Arquivo que monta o combo tipo programa
* Data de Criação: 04/02/2009

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once(CLA_SELECT);
include_once(CAM_GF_PPA_MAPEAMENTO."TPPATipoPrograma.class.php");

class ITextBoxSelectTipoPrograma extends TextBoxSelect
{
    public function ITextBoxSelectTipoPrograma()
    {
        parent::TextBoxSelect();

        $obTMapeamento	= new TPPATipoPrograma();
        $rsRecordSet    = new Recordset;

        $obTMapeamento->recuperaTodos($rsRecordSet, null, ' ORDER BY descricao');

        $this->setRotulo('Tipo Programa');
        $this->setName('inCodTipoPrograma');
        $this->setTitle('Selecione o tipo do programa.');

        $this->obTextBox->setRotulo('Tipo Programa');
        $this->obTextBox->setTitle('Selecione o tipo do programa.');
        $this->obTextBox->setName('inCodTipoProgramaTxt');
        $this->obTextBox->setId('inCodTipoProgramaTxt');
        $this->obTextBox->setSize(10);
        $this->obTextBox->setMaxLength(10);
        $this->obTextBox->setInteiro(true);

        $this->obSelect->setRotulo('sdsTipoPrograma');
        $this->obSelect->setName('inCodTipoPrograma');
        $this->obSelect->setId('inCodTipoPrograma');
        $this->obSelect->setCampoID('cod_tipo_programa');
        $this->obSelect->setCampoDesc('descricao');
        $this->obSelect->addOption('', 'Selecione');
        $this->obSelect->preencheCombo($rsRecordSet);
    }

    public function setCodTipoPrograma($inCodTipoPrograma)
    {
       $this->inCodTipoPrograma = $inCodTipoPrograma;
    }

    public function montaHTML()
    {
        if ($this->inCodTipoPrograma != "") {
           $this->obTextBox->setValue($this->inCodTipoPrograma);
           $this->obSelect->setValue($this->inCodTipoPrograma);
        }
        parent::montaHTML();
    }
}
?>
