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
    * Classe de mapeamento da tabela licitacao.membro_excluido
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19585 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-01-24 10:20:30 -0200 (Qua, 24 Jan 2007) $

    * Casos de uso: uc-03.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TLicitacaoMembroExcluido extends Persistente
{
    public function TLicitacaoMembroExcluido()
    {
        parent::Persistente();
        $this->setTabela("licitacao.membro_excluido");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_norma, numcgm, cod_comissao');

        $this->AddCampo('cod_comissao'   ,'integer'  ,false ,'',true, 'TLicitacaoComissaoMembros');
        $this->AddCampo('cod_norma'      ,'integer'  ,false ,'',true, 'TLicitacaoComissaoMembros');
        $this->AddCampo('numcgm'         ,'integer'  ,false ,'',true, 'TLicitacaoComissaoMembros');
        $this->AddCampo('timestamp'      ,'timestamp',true  ,'',false, false                     );

    }
}
