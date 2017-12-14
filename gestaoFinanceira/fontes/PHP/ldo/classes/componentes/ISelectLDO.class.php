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
 * Componente ITextBoxSelectLDO
 * Data de Criação: 20/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista: Bruno Ferreira
 * @author Desenvolvedor: Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage ldo
 * @uc

 $Id$

 */

include_once CLA_SELECT;
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDO.class.php';

class ISelectLDO extends Select
{
    public function setMostrarTodos($boVigencia)
    {
        $this->boMostrarTodos = $boVigencia;
    }

    public function setVigenciaPPA($boVigencia)
    {
        $this->boVigencia = $boVigencia;
    }

    public function __construct()
    {
        parent::__construct();

        $this->setRotulo('LDO');
        $this->setName('inAnoLDO');
        $this->setTitle('Selecione o LDO.');
        $this->setNull(true);
        $this->addOption('', 'Selecione');
        $this->setCampoID('ano');
        $this->setCampoDesc('ano');
        $this->setStyle('width: 205px');
    }

    public function setAnoLDO($inAnoLDO)
    {
       $this->inAnoLDO = $inAnoLDO;
    }

    public function montaHTML()
    {
        # Define o filtro de busca de LDO.
        if (!$this->boMostrarTodos) {
            # Calcula os anos para busca.
            $inExercicio = Sessao::getExercicio();

            # Obtem ano atual do PPA (de 1 a 4).
            $inAno = ($inExercicio + 2) % 4 + 1;

            # Obtem anos de início e fim deste PPA.
            $inAnoInicio = $inExercicio - ($inAno - 1);
            $inAnoFinal  = $inAnoInicio + 3;

            if ($this->boVigencia) {
                # Decide se busca os próximos LDOs ou os anteriores.
                if ($inExercicio == $inAnoFinal) {
                    $inAnoInicio += 4;
                    $inAnoFinal  += 4;
                } else {
                    $inAnoInicio = $inExercicio + 1;
                }
            } else {
                $inAnoInicio -= 4;
            }
            $stFiltro = " WHERE ano BETWEEN '$inAnoInicio' AND '$inAnoFinal'";
        } else {
            $stFiltro = '';
        }

        # Recupera lista de LDOs.
        $obTMapeamento = new TLDO();
        $obTMapeamento->recuperaTodos($rsListaLDO, $stFiltro, ' ORDER BY ano ');

        $this->preencheCombo($rsListaLDO);

        if ($this->inAnoLDO != '') {
           $this->setValue($this->inAnoLDO);
        }

        parent::montaHTML();
    }
}
