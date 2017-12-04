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
* Arquivo que monta o combo de orgãos
* Data de Criação: 22/09/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once(CLA_SELECT);
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php");

class ITextBoxSelectOrgao extends TextBoxSelect
{
    public function ITextBoxSelectOrgao()
    {
        parent::TextBoxSelect();

        $obTMapeamento	= new TOrcamentoOrgao();
        $rsRecordSet    = new Recordset;

        $obTMapeamento->recuperaRelacionamento($rsRecordSet, " AND OO.exercicio = '" . sessao::read('exercicio') . "'", ' ORDER BY OO.nom_orgao');

        $this->setRotulo('Orgão');
        $this->setName('inCodOrgao');
        $this->setTitle('Selecione o orgão.');

        $this->obTextBox->setRotulo('Orgão');
        $this->obTextBox->setTitle('Selecione o orgão.');
        $this->obTextBox->setName('inCodOrgaoTxt');
        $this->obTextBox->setId('inCodOrgaoTxt');
        $this->obTextBox->setSize(10);
        $this->obTextBox->setMaxLength(10);
        $this->obTextBox->setInteiro(true);

        $this->obSelect->setRotulo('sdsOrgão');
        $this->obSelect->setName('inCodOrgao');
        $this->obSelect->setId('inCodBanco');
        $this->obSelect->setCampoID('num_orgao');
        $this->obSelect->setCampoDesc('nom_orgao');
        $this->obSelect->addOption('', 'Selecione');
        $this->obSelect->preencheCombo($rsRecordSet);
        $this->obSelect->setStyle('width: 400px');
    }

    public function setCodOrgao($inCodOrgao)
    {
       $this->inCodOrgao = $inCodOrgao;
    }

    public function montaHTML()
    {
        if ($this->inCodOrgao != "") {
           $this->obTextBox->setValue($this->inCodOrgao);
           $this->obSelect->setValue($this->inCodOrgao);
        }
        parent::montaHTML();
    }
}
?>
