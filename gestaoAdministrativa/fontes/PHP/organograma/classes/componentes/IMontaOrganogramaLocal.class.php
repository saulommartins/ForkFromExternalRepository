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
    * Componente para manipular os Locais do Organograma.
    * Data de Criação: 29/12/2008

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Diogo Zarpelon

    * @package URBEM
    * @subpackage

    */

include_once(CLA_SELECT);

class IMontaOrganogramaLocal extends Select
{
    public function IMontaOrganogramaLocal()
    {
        parent::Select();

        $this->setName     ('inCodLocal');
        $this->setId       ('inCodLocal');
        $this->setRotulo   ('Local');
        $this->setStyle    ('width: 270px;');
        $this->setTitle    ('Selecione a descrição do Local.');
        $this->setNull     (true);
        $this->addOption   ('', 'Selecione');
        $this->setCampoId  ('cod_local');
        $this->setCampoDesc('descricao');
    }

    public function montaHTML()
    {
        include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaLocal.class.php';
        $obTOrganogramaLocal = new TOrganogramaLocal;
        # Cria um RecordSet com todos os Locais cadastrados.
        $obTOrganogramaLocal->recuperaTodos($rsLocal,'',' ORDER BY descricao');
        $this->preencheCombo($rsLocal);
        parent::montaHTML();
    }

    public function geraFormulario(&$obFormulario)
    {
        # Adiciona o componente no formulário referenciado.
        $obFormulario->addComponente($this);
    }

}

?>
