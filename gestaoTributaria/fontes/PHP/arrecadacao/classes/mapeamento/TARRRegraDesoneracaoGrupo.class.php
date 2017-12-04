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
    * Classe de mapeamento da tabela ARRECADACAO.REGRA_DESONERACAO_GRUPO
    * Data de Criação: 03/10/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: $

* Casos de uso: uc-05.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRRegraDesoneracaoGrupo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRRegraDesoneracaoGrupo()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.regra_desoneracao_grupo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_grupo, ano_exercicio');

        $this->AddCampo('cod_grupo', 'integer', true, '', true, true );
        $this->AddCampo('ano_exercicio', 'varchar', true, '4', true, true  );
        $this->AddCampo('cod_modulo', 'integer', false, '', false, true );
        $this->AddCampo('cod_biblioteca', 'integer', true, '', false, true );
        $this->AddCampo('cod_funcao', 'integer', true, '', false, true );
    }

}
?>
