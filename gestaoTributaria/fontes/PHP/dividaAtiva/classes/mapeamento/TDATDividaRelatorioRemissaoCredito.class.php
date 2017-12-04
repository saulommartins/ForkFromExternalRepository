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
    * Classe de mapeamento da tabela divida.relatorio_remissao_divida_credito
    * Data de Criação: 10/05/2010

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Eduardo Paculski Schitz
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATDividaRelatorioRemissaoCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaRelatorioRemissaoCredito()
    {
        parent::Persistente();
        $this->setTabela('divida.relatorio_remissao_credito');

        $this->setCampoCod('cod_lancamento');

        $this->AddCampo('cod_lancamento', 'integer', true, '', true, true);
    }

}// end of class

?>
