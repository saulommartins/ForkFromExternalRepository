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
 * Classe de mapeamento da tabela ppa.acao_norma_exclusao
 * Data de Criação: 24/10/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 * $Id: TPPAAcaoNormaExclusao.class.php 38190 2009-02-16 18:56:27Z janio.magalhaes $

 * Casos de uso: uc-02.09.04
 */

class TPPAAcaoNormaExclusao extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.acao_norma_exclusao');

        $this->setCampoCod('cod_acao');
        $this->setComplementoChave('timestamp_acao_dados,cod_norma');

        $this->addCampo('cod_acao', 'integer', true, '', true, false);
        $this->addCampo('timestamp_acao_dados', 'timestamp', true, '', false, true);
        $this->addCampo('cod_norma', 'integer', true, '', true, true);
    }
}

?>
