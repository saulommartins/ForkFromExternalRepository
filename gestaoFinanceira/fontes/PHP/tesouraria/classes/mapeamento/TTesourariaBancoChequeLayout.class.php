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
 * Mapeamento da tabela tesouraria.banco_cheque_layout
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaBancoChequeLayout extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaBancoChequeLayout
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.banco_cheque_layout');
        $this->setCampoCod        ('cod_banco');
        $this->setComplementoChave('');

        $this->AddCampo('cod_banco'          ,'integer', true, '',true ,true );
        $this->AddCampo('col_valor_numerico' ,'integer', true, '',false,false);
        $this->AddCampo('col_extenso_1'      ,'integer', true, '',false,false);
        $this->AddCampo('col_extenso_2'      ,'integer', true, '',false,false);
        $this->AddCampo('col_favorecido'     ,'integer', true, '',false,false);
        $this->AddCampo('col_cidade'         ,'integer', true, '',false,false);
        $this->AddCampo('col_dia'            ,'integer', true, '',false,false);
        $this->AddCampo('col_mes'            ,'integer', true, '',false,false);
        $this->AddCampo('col_ano'            ,'integer', true, '',false,false);
        $this->AddCampo('lin_valor_numerico' ,'integer', true, '',false,false);
        $this->AddCampo('lin_extenso_1'      ,'integer', true, '',false,false);
        $this->AddCampo('lin_extenso_2'      ,'integer', true, '',false,false);
        $this->AddCampo('lin_favorecido'     ,'integer', true, '',false,false);
        $this->AddCampo('lin_cidade_data'    ,'integer', true, '',false,false);
    }

}
