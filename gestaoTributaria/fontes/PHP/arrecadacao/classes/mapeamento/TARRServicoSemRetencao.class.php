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
    * Classe de mapeamento da tabela ARRECADACAO.SERVICO_SEM_RETENCAO
    * Data de Criação: 23/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRServicoSemRetencao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.22
*/

/*
$Log$
Revision 1.2  2007/02/22 12:16:35  cassiano
Alteração na escrituração de receita, inclusão do campo ocorrencia em algumas tabelas.

Revision 1.1  2006/10/26 14:06:43  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRServicoSemRetencao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRServicoSemRetencao()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.servico_sem_retencao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_atividade,cod_servico,inscricao_economica,ocorrencia,timestamp');

        $this->AddCampo('cod_atividade', 'integer', true, '', true, true );
        $this->AddCampo('cod_servico', 'integer', true, '', true, true );
        $this->AddCampo('inscricao_economica', 'integer', true, '', true, true  );
        $this->AddCampo('ocorrencia', 'integer', true, '', true, true  );
        $this->AddCampo('timestamp', 'timestamp', false, '', true, true  );
        $this->AddCampo('valor_declarado', 'numeric', true, '14,2', false, false );
        $this->AddCampo('valor_deducao', 'numeric', true, '14,2', false, false );
        $this->AddCampo('valor_deducao_legal', 'numeric', true, '14,2', false, false );
        $this->AddCampo('valor_lancado', 'numeric', true, '14,2', false, false );
        $this->AddCampo('aliquota', 'numeric', true, '14,2', false, false );
    }

}
?>
