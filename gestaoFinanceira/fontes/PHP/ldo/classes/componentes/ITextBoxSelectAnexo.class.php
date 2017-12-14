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
 * Componente ITextBoxSelectAnexo
 * Data de Criação: 20/02/2008
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <janio.magalhaes>
 * @package gestaoFinanceira
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ajuste de Anexo
 */

include_once CLA_SELECT;
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAnexo.class.php';

class ITextBoxSelectAnexo extends TextBoxSelect
{
    /**
     * Código do Anexo
     * @var int
     */
    private $inCodAnexo;
    private $boDisabled;

    /**
     * Método construtor
     *
     */
    public function __construct()
    {
        parent::__construct();

        # Obtém lista de Anexos.
        $obMapeamento = new TLDOAnexo();
        $obMapeamento->recuperaListaAnexo($rsListaAnexo, '', ' ORDER BY cod_anexo ASC');

        $this->setRotulo('Anexo');
        $this->setName('inCodAnexo');
        $this->setTitle('Selecione o Anexo.');

        $this->obTextBox->setRotulo('Anexo');
        $this->obTextBox->setTitle('Selecione o Anexo.');
        $this->obTextBox->setName('inCodAnexoTxt');
        $this->obTextBox->setId('inCodAnexoTxt');
        $this->obTextBox->setSize(10);
        $this->obTextBox->setMaxLength(10);
        $this->obTextBox->setInteiro(true);

        $this->obSelect->setRotulo('Anexo');
        $this->obSelect->setName('inCodAnexo');
        $this->obSelect->setId('inCodAnexo');
        $this->obSelect->setCampoID('cod_anexo');
        $this->obSelect->setCampoDesc('nom_acao');
        $this->obSelect->setStyle('width: 205px');
        $this->obSelect->addOption('', 'Selecione');
        $this->obSelect->preencheCombo($rsListaAnexo);
    }

    /**
     * Armazena o código do Anexo
     *
     * @param int $inCodAnexo
     */
    public function setCodAnexo($inCodAnexo)
    {
       $this->inCodAnexo = $inCodAnexo;
    }

    /**
     * Monta infecade do componente
     */
    public function montaHTML()
    {
        if ($this->inCodAnexo != '') {
            $this->obTextBox->setValue($this->inCodAnexo);
            $this->obSelect->setValue($this->inCodAnexo);
        }

        parent::montaHTML();
    }

    /**
     * Define estado do componente como ativado ou desativado
     * @param $boDisabled estado do componente
     */
    public function setDisabled($boDisabled)
    {
        $this->obTextBox->setDisabled($boDisabled);
        $this->obSelect->setDisabled($boDisabled);
        $this->boDisabled = $boDisabled;
    }

    /**
     * Recupera o estado do componente como ativado ou desativado
     * @return boolean estado do componente
     */
    public function getDisabled()
    {
        return $this->boDisabled;
    }
}
