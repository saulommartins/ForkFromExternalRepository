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
    * Data de Criação: 13/11/2006

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

class ICombosRelatorio
{
    public $obCmbGestao;
    public $obCmbModulo;
    public $obCmbRelatorio;

    public function ICombosRelatorio()
    {
        $this->obCmbGestao = new Select();
        $this->obCmbGestao->setName     ('inCodigoGestao');
        $this->obCmbGestao->setRotulo   ('Relatório');
        $this->obCmbGestao->setCampoId  ('cod_gestao');
        $this->obCmbGestao->setCampoDesc('nom_gestao');
        $this->obCmbGestao->addOption   ('', 'Selecione uma Gestão');
        $this->obCmbGestao->setStyle    ('width:300px');

        $this->obCmbModulo = new Select();
        $this->obCmbModulo->setName     ('inCodigoModulo');
        $this->obCmbModulo->setId       ('inCodigoModulo');
        $this->obCmbModulo->setRotulo   ('Relatório');
        $this->obCmbModulo->setCampoId  ('cod_modulo');
        $this->obCmbModulo->setCampoDesc('nom_modulo');
        $this->obCmbModulo->addOption   ('', 'Selecione um Módulo');
        $this->obCmbModulo->setStyle    ('width:300px');

        $this->obCmbRelatorio = new Select();
        $this->obCmbRelatorio->setName     ('inCodigoRelatorio');
        $this->obCmbRelatorio->setId       ('inCodigoRelatorio');
        $this->obCmbRelatorio->setRotulo   ('Relatório');
        $this->obCmbRelatorio->setCampoId  ('cod_relatorio');
        $this->obCmbRelatorio->setCampoDesc('nom_relatorio');
        $this->obCmbRelatorio->addOption   ('', 'Selecione um Relatório');
        $this->obCmbRelatorio->setStyle    ('width:300px');
    }

    public function setNull($boNull)
    {
        $this->obCmbGestao->setNull($boNull);
        $this->obCmbModulo->setNull($boNull);
        $this->obCmbRelatorio->setNull($boNull);
    }

    public function setRotulo($stRotulo)
    {
        $this->obCmbGestao->setRotulo($stRotulo);
        $this->obCmbModulo->setRotulo($stRotulo);
        $this->obCmbRelatorio->setRotulo($stRotulo);
    }

    public function preencheGestao()
    {
        include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoGestao.class.php');
        $obTAdministracaoGestao = new TAdministracaoGestao();
        $obTAdministracaoGestao->recuperaTodos($rsGestao,'','ordem');
        $this->obCmbGestao->preencheCombo($rsGestao);
    }

    public function geraFormulario(&$obFormulario)
    {
        $stPagina = CAM_FW_INSTANCIAS.'processamento/OCPreencheCombo.php?'.Sessao::getId();
        $this->preencheGestao();
        $stParametros = "";
        $stParametros = "'stPersistente=TAdministracaoModulo&stIdCombo=".$this->obCmbModulo->getId();
        $stParametros .= "&stCampoId=cod_modulo&stCampoDesc=nom_modulo";
        $stParametros .= "&cod_gestao='+this.value";

        $stParametrosRel = "'stPersistente=TAdministracaoModulo&stIdCombo=".$this->obCmbRelatorio->getId()."'";

        $this->obCmbGestao->obEvento->setOnChange("preencheComboAjax('$stPagina',$stParametros,'preencher');limpaSelect(document.getElementById('".$this->obCmbRelatorio->getId()."'),1);");

        $stParametros = "";
        $stParametros = "'stPersistente=TAdministracaoRelatorio&stIdCombo=".$this->obCmbRelatorio->getId()."&stCampoId=cod_relatorio&stCampoDesc=nom_relatorio";
        $stParametros .= "&cod_modulo='+this.value+'&cod_gestao='+document.".$obFormulario->obForm->getName().".".$this->obCmbGestao->getName().".value";
        $this->obCmbModulo->obEvento->setOnChange("preencheComboAjax('$stPagina',$stParametros,'preencher');");

        $obFormulario->addComponente($this->obCmbGestao);
        $obFormulario->addComponente($this->obCmbModulo);
        $obFormulario->addComponente($this->obCmbRelatorio);
    }
}
