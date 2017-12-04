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
 * Classe de Negocio do 02.10.04 - Manter Receita
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.04 - Manter Receita
 */

include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaRecurso.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOReceita.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOReceitaDados.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOReceitaRecurso.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOReceitaInativaNorma.class.php';
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';

class RLDOManterReceita extends RLDOPadrao implements IRLDOPadrao
{

private $obTLDOReceita;
private $obTLDOReceitaDados;
private $obTLDOReceitaRecurso;
private $obTLDOReceitaInativaReceita;
private $inCodReceitaPPA;
private $chAno;
private $inCodEntidade;
private $inCodConta;
private $exercicio;
private $inCodPPA;

    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    public function inicializar()
    {
        $this->obTLDOReceita = new TLDOReceita;
        $this->obTLDOReceitaDados = new TLDOReceitaDados;
        $this->obTLDOReceitaRecurso = new TLDOReceitaRecurso;
        $this->obTLDOReceitaInativaReceita = new TLDOReceitaInativaNorma;
    }

    public function incluir(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        #INICIA TRANSAÇÂO
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $stFiltro = " where cod_receita_ppa = ".$arArgs['inNumReceita']."AND ativo = 't'";
        $this->obTLDOReceita->recuperaTodos($rsReceita, $stFiltro, null, $boTransacao);
        if (!$rsReceita->Eof()) {
            throw new RLDOExcecao( 'Receita já incluida' );
        }
        $this->obTLDOReceita->proximoCod($arArgs['inCodReceita'], $boTransacao);
        $this->obTLDOReceitaDados->proximoCod($arArgs['inCodReceitaDados'], $boTransacao);

        $obErro = $this->incluirReceita( $arArgs, $boTransacao );

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao( 'Ocorreu um erro ao incluir receita' );
        }

        $obErro = $this->incluirReceitaDados($arArgs, $boTransacao);

        if ($obErro->ocorreu()) {
           throw new RLDOExcecao( 'Ocorreu um erro ao inserir dados na receita' );
        }

        $obErro = $this->incluirReceitaRecurso( $arArgs, $boTransacao );

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao( 'Ocorreu um erro ao incluir recurso(s) na receita' );
        }
        #FECHA TRANSAÇÂO
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->incluirReceita);

    return true;
    }

    public function recuperarTotalReceitaLDO(array $arArgs)
    {
      $stFiltro =  $this->recuperarFiltro($arArgs);
      $this->obTLDOReceita->recuperaTotalReceitaLDO($rsReceita, $stFiltro, $stOrderBy = '', $obTransacao = '');

      return $rsReceita;
    }

    public function incluirReceita(array $arArgs, $boTransacao)
    {
        $rsReceita = $this->recuperaReceitaPPA($arArgs['inNumReceita'],$boTransacao);
        $inTotalReceita = $this->somaRecurso($arArgs['arValorRecurso']);

        $this->obTLDOReceita->setDado('cod_receita'    ,$arArgs['inCodReceita']);
        $this->obTLDOReceita->setDado('ano'            ,$arArgs['stAnoLDO']);
        $this->obTLDOReceita->setDado('cod_receita_ppa',$rsReceita->getCampo('cod_receita'));
        $this->obTLDOReceita->setDado('cod_entidade'   ,$rsReceita->getCampo('cod_entidade'));
        $this->obTLDOReceita->setDado('cod_conta'      ,$rsReceita->getCampo('cod_conta'));
        $this->obTLDOReceita->setDado('exercicio'      ,$rsReceita->getCampo('exercicio'));
        $this->obTLDOReceita->setDado('cod_ppa'        ,$rsReceita->getCampo('cod_ppa'));
        $this->obTLDOReceita->setDado('valor_total'    ,$inTotalReceita);
        $this->obTLDOReceita->setDado('ativo'          ,'t');
        $obErro = $this->obTLDOReceita->inclusao($boTransacao);

        return $obErro;

    }

    public function excluirReceita(array $arArgs, $boTransacao)
    {
        $rsReceita = $this->recuperaReceitaPPA($arArgs['inNumReceita'],$boTransacao);
        $this->obTLDOReceita->setDado('cod_receita'    ,$arArgs['inCodReceita']);
        $this->obTLDOReceita->setDado('ano'            ,$arArgs['stAnoLDO']);
        $this->obTLDOReceita->setDado('cod_receita_ppa',$rsReceita->getCampo('cod_receita'));
        $this->obTLDOReceita->setDado('cod_entidade'   ,$rsReceita->getCampo('cod_entidade'));
        $this->obTLDOReceita->setDado('cod_conta'      ,$rsReceita->getCampo('cod_conta'));
        $this->obTLDOReceita->setDado('exercicio'      ,$rsReceita->getCampo('exercicio'));
        $this->obTLDOReceita->setDado('cod_ppa'        ,$rsReceita->getCampo('cod_ppa'));
        $this->obTLDOReceita->setDado('valor_total'    ,$arArgs['inTotalReceita']);
        $this->obTLDOReceita->setDado('ativo'          ,'f');
        $obErro = $this->obTLDOReceita->alteracao($boTransacao);

        return $obErro;

    }

    public function incluirReceitaRecurso(array $arArgs, $boTransacao)
    {
        for ($x=0; $x < $arArgs['inSizeRecursos']; $x++) {
            $this->obTLDOReceitaRecurso->setDado('cod_receita',$arArgs['inCodReceita']);
            $this->obTLDOReceitaRecurso->setDado('cod_receita_dados',$arArgs['inCodReceitaDados']);
            $this->obTLDOReceitaRecurso->setDado('cod_recurso',$arArgs['arCodRecurso'][$x]);
            $this->obTLDOReceitaRecurso->setDado('exercicio',Sessao::read('exercicio'));
            $this->obTLDOReceitaRecurso->setDado('valor',$arArgs['arValorRecurso'][$x]);
            $obErro = $this->obTLDOReceitaRecurso->inclusao($boTransacao);
        }

        return $obErro;
    }

    public function incluirReceitaDados(array $arArgs, $boTransacao)
    {
        $this->obTLDOReceitaDados->setDado('cod_receita',$arArgs['inCodReceita']);
        $this->obTLDOReceitaDados->setDado('cod_receita_dados',$arArgs['inCodReceitaDados']);
        $this->obTLDOReceitaDados->setDado('cod_norma',$arArgs['inCodNorma']);
        $obErro = $this->obTLDOReceitaDados->inclusao($boTransacao);

        return $obErro;
    }

    public function incluirReceitaInativaNorma(array $arArgs, $boTransacao)
    {
        $this->obTLDOReceitaInativaReceita->setDado('cod_receita',$arArgs['inCodReceita']);
        $this->obTLDOReceitaInativaReceita->setDado('cod_norma',$arArgs['inCodNorma']);
        $obErro = $this->obTLDOReceitaInativaReceita->inclusao($boTransacao);

        return $obErro;
    }

    public function excluir(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        #INICIA TRANSAÇÂO
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ($arArgs['inCodNorma']) {
            $obErro = $this->excluirReceita( $arArgs, $boTransacao );

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao( 'Ocorreu um erro ao excluir receita' );
            }

            $obErro = $this->incluirReceitaInativaNorma( $arArgs, $boTransacao );

            if ($obErro->ocorreu()) {
               throw new RLDOExcecao( 'Ocorreu um erro ao excluir norma inativa' );
            }
        } else {
            $obErro = $this->excluirReceitaRecursoNaoHomologada( $arArgs, $boTransacao );

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao( 'Ocorreu um erro ao excluir receita recurso' );
            }

            $obErro = $this->excluirReceitaDadosNaoHomologada( $arArgs, $boTransacao );

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao( 'Ocorreu um erro ao excluir receita Dados' );
            }

            $obErro = $this->excluirReceitaNaoHomologada( $arArgs, $boTransacao );

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao( 'Ocorreu um erro ao excluir receita' );
            }

        }
        #FECHA TRANSAÇÂO
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->alterarReceita);

    return true;

    }

    public function excluirReceitaRecursoNaoHomologada(array $arArgs, $boTransacao)
    {
        $this->obTLDOReceitaRecurso->setDado('cod_receita',$arArgs['inCodReceita']);
       // $this->obTLDOReceitaRecurso->setDado('cod_receita_dados',$arArgs['inCodReceitaDados']);
        $obErro = $this->obTLDOReceitaRecurso->exclusao($boTransacao);

        return $obErro;
    }

    public function excluirReceitaDadosNaoHomologada(array $arArgs, $boTransacao)
    {
        //$this->obTLDOReceitaDados->setDado('cod_receita_dados',$arArgs['inCodReceitaDados']);
        $this->obTLDOReceitaDados->setDado('cod_receita',$arArgs['inCodReceita']);
        $obErro = $this->obTLDOReceitaDados->exclusao($boTransacao);

        return $obErro;
    }

    public function excluirReceitaNaoHomologada(array $arArgs, $boTransacao)
    {
        $this->obTLDOReceita->setDado('cod_receita'    ,$arArgs['inCodReceita']);
        $obErro = $this->obTLDOReceita->exclusao($boTransacao);

        return $obErro;
    }

    public function alterar(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        #INICIA TRANSAÇÂO
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $this->obTLDOReceitaDados->proximoCod($arArgs['inCodReceitaDados'], $boTransacao);

        $obErro = $this->alterarReceita( $arArgs, $boTransacao );

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao alterar receita');
        }

        $obErro = $this->incluirReceitaDados($arArgs, $boTransacao);

        if ($obErro->ocorreu()) {
           throw new RLDOExcecao( 'Ocorreu um erro ao alterar dados na receita' );
        }

        $obErro = $this->incluirReceitaRecurso($arArgs, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao( 'Ocorreu um erro ao alterar recurso(s) na receita' );
        }

        #FECHA TRANSAÇÂO
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->alterarReceita);

        return true;
    }

    public function alterarReceita(array $arArgs, $boTransacao)
    {
        $rsReceita = $this->recuperaReceitaPPA($arArgs['inNumReceita'],$boTransacao);
        $inTotalReceita = $this->somaRecurso($arArgs['arValorRecurso']);

        $this->obTLDOReceita->setDado('cod_receita'    ,$arArgs['inCodReceita']);
        $this->obTLDOReceita->setDado('ano'            ,$arArgs['stAnoLDO']);
        $this->obTLDOReceita->setDado('cod_receita_ppa',$rsReceita->getCampo('cod_receita'));
        $this->obTLDOReceita->setDado('cod_entidade'   ,$rsReceita->getCampo('cod_entidade'));
        $this->obTLDOReceita->setDado('cod_conta'      ,$rsReceita->getCampo('cod_conta'));
        $this->obTLDOReceita->setDado('exercicio'      ,$rsReceita->getCampo('exercicio'));
        $this->obTLDOReceita->setDado('cod_ppa'        ,$rsReceita->getCampo('cod_ppa'));
        $this->obTLDOReceita->setDado('valor_total'    ,$inTotalReceita);
        $this->obTLDOReceita->setDado('ativo'          ,'t');
        $obErro = $this->obTLDOReceita->alteracao($boTransacao);

        return $obErro;

    }

    public function somaRecurso($arRecursos)
    {
        $soma = 0;
        foreach ($arRecursos as $valorRecurso) {
            $valorRecurso = str_replace( '.', '',  $valorRecurso);
            $valorRecurso = str_replace( ',', '.', $valorRecurso);
            $soma = $soma + $valorRecurso;
        }

        return $soma;
    }

    public function recuperaReceitaPPA($inCodReceita,$boTransacao ='')
    {
        $obTPPAReceita = new TPPAReceita();
        $stFiltro = ' where cod_receita = '.$inCodReceita;
        $obTPPAReceita->recuperaTodos($rsReceita, $stFiltro, null, $boTransacao);

        return $rsReceita;
    }

    /**
     * @param array arParametros
     */
    public function recuperarRecurso(array $arArgs)
    {
        $obTPPAReceitaRecurso = new TPPAReceitaRecurso();
        $obTPPAReceitaRecurso->recuperaReceitaRecurso($rsRecurso, $this->recuperarFiltro($arArgs));

        return $rsRecurso;
    }

    public function recuperarRecursosReceita($inCodReceitaDados)
    {
        $stFiltro = " where receita_recurso.cod_receita_dados =".$inCodReceitaDados;
        $this->obTLDOReceitaRecurso->recuperaDadosRecurso($rsRecursos, $stFiltro);

        return $rsRecursos;

    }

    public function recuperarReceitaLDO(array $arArgs = null, $obTransacao = null)
    {

        $stFiltro = " WHERE ";
        if ($arArgs['inNumReceita']) {
            $stFiltro .=  " receita.cod_receita_ppa = " .$arArgs['inNumReceita'];
            $stFiltro .=  " AND ";
        }
        $stFiltro .= " receita.ativo = 't'";
        $anoLDO = sessao::read('exercicio')+1;
        $stFiltro .= " AND receita.ano = ".$anoLDO;

        $this->obTLDOReceita->recuperaDadosReceita($rsReceita, $stFiltro, null, $obTransacao);

        return $rsReceita;
    }

    public function recuperarFiltro(array $arArgs = null)
    {
        $arFiltro = array();
        $stFiltro = '';

        if ($arArgs['inNumReceita'] != '') {
            $arFiltro[] = ' prr.cod_receita = ' .$arArgs['inNumReceita'];
        }

        if ($arArgs['inCodRecurso'] != '') {
            $arFiltro[] = ' prr.cod_recurso = ' .$arArgs['inCodRecurso'];
        }

        if ($arArgs['inNumRecurso'] != '') {
            $arFiltro[] = ' prr.cod_recurso = ' .$arArgs['inNumRecurso'];
        }

        if ($arArgs['inNumReceita2'] != '') {
            $arFiltro[] = ' receita.cod_receita_ppa = ' .$arArgs['inNumReceita'];
        }

        if ($arArgs['stAno'] != '') {
            $arFiltro[] = ' ano = ' .$arArgs['stAno'];
            $arFiltro[] = " ativo = 't'";
        }

        if ($arFiltro) {
            foreach ($arFiltro as $inIndice => $stValor) {
                if ($inIndice == 0) {
                    $stFiltro .= $stValor . "\n";
                } else {
                    $stFiltro .= ' AND ' . $stValor . "\n";
                }
            }

            $stFiltro = ' WHERE ' . $stFiltro;
        }

        return $stFiltro;
    }
}
