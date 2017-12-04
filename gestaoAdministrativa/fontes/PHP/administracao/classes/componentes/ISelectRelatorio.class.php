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
    * Componente para selecção de arquivo para relatório
    * Data de Criação: 16/11/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage

    $Revision: 17755 $
    $Name$
    $Author: cassiano $
    $Date: 2006-11-16 14:30:28 -0200 (Qui, 16 Nov 2006) $

    * Casos de uso: uc-01.01.00
*/

class ISelectRelatorio extends Select
{
    public $obMapeamento;

    public function ISelectRelatorio()
    {
        parent::Select();
        $this->setName('inCodigoRelatorio');
        $this->addOption('','Selecione um Relatório');
        $this->setStyle('width:300px');
        $this->setCampoId('cod_relatorio');
        $this->setCampoId('nom_relatorio');
        $this->setRotulo('Relatorio');
        $this->setNull(false);
    }

    public function setMapeamento(&$valor) { $this->obMapeamento = $valor; }

    public function buscarRelatorios()
    {
        if ( is_object($this->obMapeamento) or is_string($this->obMapeamento)) {
            $stMapeamento = is_object($this->obMapeamento) ? $this->obMapeamento->getTabela() : $this->obMapeamento;
            include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoRelatorio.class.php');
            $obTAdministracaoRelatorio = new TAdministracaoRelatorio();
            $obTAdministracaoRelatorio->setDado('mapeamento',$stMapeamento);
            $obTAdministracaoRelatorio->recuperaRelacionamento($rsRelatorio,'','nom_relatorio');
            while (!$rsRelatorio->eof()) {
                $this->addOption($rsRelatorio->getCampo('cod_relatorio'),$rsRelatorio->getCampo('nom_relatorio'));
                $rsRelatorio->proximo();
            }
        }
    }

    public function montaHtml()
    {
        $this->buscarRelatorios();
        parent::montaHtml();
    }
}
?>
