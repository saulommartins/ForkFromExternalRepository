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
* Classe de negócio CSE
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.07.86, uc-01.07.87, uc-01.07.88, uc-01.07.89, uc-01.07.90, uc-01.07.91, uc-01.07.92, uc-01.07.93, uc-01.07.94, uc-01.07.95, uc-01.07.96, uc-01.07.97, uc-01.07.98, uc-01.07.98
*/

    class cse
    {
        /*** Variáveis da classe ***/
            var $codigo;
        /*** Método Construtor ***/
            function cse()
            {
                $this->codigo = "";
            }

        /*** Método que inclui um domicílio ***/
            function incluiDomicilio($var)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $insert =   "INSERT INTO
                            cse.domicilio
                            (
                            cod_domicilio,
                            cod_uf,
                            cod_municipio,
                            cod_localidade,
                            cod_tipo_domicilio,
                            cod_construcao,
                            cod_situacao,
                            cod_tipo_logradouro,
                            logradouro,
                            numero,
                            complemento,
                            bairro,
                            cep,
                            telefone,
                            qtd_comodos,
                            energia_eletrica,
                            qtd_residentes,
                            qtd_gravidas,
                            qtd_maes_amamentando,
                            qtd_deficientes,
                            cod_cobertura,
                            cod_abastecimento,
                            cod_tratamento,
                            cod_esgotamento,
                            cod_destino_lixo
                            )
                            VALUES
                            (
                             ".$var[codDomicilio].",
                             ".$var[codEstado].",
                             ".$var[codMunicipio].",
                             ".$var[codLocalidade].",
                             ".$var[codTipoDomicilio].",
                             ".$var[codConstrucao].",
                             ".$var[codSituacao].",
                             ".$var[codTipoLogradouro].",
                            '".$var[logradouro]."',
                             ".$var[numero].",
                            '".$var[complemento]."',
                            '".$var[bairro]."',
                            '".$var[cep]."',
                            '".$var[telefone]."',
                             ".$var[qtdComodos].",
                            '".$var[energia]."',
                             ".$var[qtdResidentes].",
                             ".$var[qtdGravidas].",
                             ".$var[qtdMaesAmamentando].",
                             ".$var[qtdDeficientes].",
                             ".$var[codCobertura].",
                             ".$var[codAbastecimento].",
                             ".$var[codTratamentoAgua].",
                             ".$var[codEsgotamento].",
                             ".$var[codDestinoLixo]."
                            )";
                echo $insert;
                $fim = $dbConfig->executaSql($insert);
                $dbConfig->fechaBd();
                if ($fim) {
                    return true;
                } else {
                    return false;
                }
            }

        /*** Método que altera o Domicílio selecionado ***/
            function alteraDomicilio($var)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $update =   "UPDATE
                            cse.domicilio
                            SET
                            cod_uf = ".$var[codEstado].",
                            cod_municipio = ".$var[codMunicipio].",
                            cod_localidade = ".$var[codLocalidade].",
                            cod_tipo_domicilio = ".$var[codTipoDomicilio].",
                            cod_construcao = ".$var[codConstrucao].",
                            cod_situacao = ".$var[codSituacao].",
                            cod_tipo_logradouro = ".$var[codTipoLogradouro].",
                            logradouro = '".$var[logradouro]."',
                            numero = ".$var[numero].",
                            complemento = '".$var[complemento]."',
                            bairro = '".$var[bairro]."',
                            cep = '".$var[cep]."',
                            telefone = '".$var[telefone]."',
                            qtd_comodos = ".$var[qtdComodos].",
                            energia_eletrica = '".$var[energia]."',
                            qtd_residentes = ".$var[qtdResidentes].",
                            qtd_gravidas = ".$var[qtdGravidas].",
                            qtd_maes_amamentando = ".$var[qtdMaesAmamentando].",
                            qtd_deficientes = ".$var[qtdDeficientes].",
                            cod_cobertura = ".$var[codCobertura].",
                            cod_abastecimento = ".$var[codAbastecimento].",
                            cod_tratamento = ".$var[codTratamentoAgua].",
                            cod_esgotamento = ".$var[codEsgotamento].",
                            cod_destino_lixo = ".$var[codDestinoLixo]."
                            WHERE
                            cod_domicilio = ".$var[codDomicilio];
                //echo $update."<br>";
                $result = $dbConfig->executaSql($update);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }

        /*** Método que inclui uma instituição educacional ***/
            function incluiInstituicao($var)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $insert =   "INSERT INTO
                            cse.instituicao_educacional
                            (
                            cod_instituicao,
                            nom_instituicao
                            )
                            VALUES
                            (
                            ".$var[codInstituicao].",
                            '".$var[nomInstituicao]."'
                            )";
                //echo $insert."<br>";
                $result = $dbConfig->executaSql($insert);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

        /*** Método que altera uma instituição educacional ***/
            function alteraInstituicao($var)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $update =   "UPDATE
                            cse.instituicao_educacional
                            SET
                            nom_instituicao = '".$var[nomInstituicao]."'
                            WHERE
                            cod_instituicao = ".$var[codInstituicao];
                //echo $update."<br>";
                $result = $dbConfig->executaSql($update);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

        /*** Método que exclui uma instituição educacional ***/
            function excluiInstituicao($codInstituicao)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $delete =   "DELETE
                            FROM
                            cse.instituicao_educacional
                            WHERE
                            cod_instituicao = ".$codInstituicao;
                //echo $delete."<br>";
                $result = $dbConfig->executaSql($delete);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

        /*** Método que inclui uma questão de censo ***/
            function incluiQuestao($var)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $codQuestao = pegaID('cod_questao',"cse.questao_censo");
                $insert =   "INSERT INTO
                            cse.questao_censo
                            (
                            cod_questao,
                            exercicio,
                            nom_questao,
                            ordem,
                            tipo,
                            valor_padrao
                            )
                            VALUES
                            (
                            ".$codQuestao.",
                            ".$var[anoExercicio].",
                            '".$var[nomQuestao]."',
                            ".$var[ordemQuestao].",
                            '".$var[tipo]."',
                            '".$var[valorPadrao]."'
                            )";
                //echo $insert."<br>";
                $result = $dbConfig->executaSql($insert);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

        /*** Método que altera uma questão de censo ***/
            function alteraQuestao($var)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $update =   "UPDATE
                            cse.questao_censo
                            SET
                            exercicio = ".$var[anoExercicio].",
                            nom_questao = '".$var[nomQuestao]."',
                            ordem = ".$var[ordemQuestao].",
                            tipo = '".$var[tipo]."',
                            valor_padrao = '".$var[valorPadrao]."'
                            WHERE
                            cod_questao = ".$var[codQuestao];
                echo $update."<br>";
                $result = $dbConfig->executaSql($update);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

        /*** Método que exclui uma questão de censo ***/
            function excluiQuestao($codQuestao)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $delete =   "DELETE FROM
                            cse.questao_censo
                            WHERE
                            cod_questao = ".$codQuestao;
                //echo $delete."<br>";
                $result = $dbConfig->executaSql($delete);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

        /*** Mátodo que inclui um novo cidadão ***/
            function incluiCidadao()
            {
                global $sessao;
                global $_POST;
                $codCidadao = pegaId("cod_cidadao","cse.cidadao");
                $sessao->transf4[cidadao][codCidadao] = $codCidadao;
                $stSql = $this->montaIncluiCidadao();
                $stSql = str_replace("<br>", "", $stSql);
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBD();
                $result = $dbConfig->executaSql($stSql);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

            function alteraCidadao()
            {
                global $sessao;
                global $_POST;
                $stSql  = $this->montaExcluiCidadao();
                $stSql .= $this->montaIncluiCidadao();
                $stSql = str_replace("<br>", "", $stSql);
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBD();
                $result = $dbConfig->executaSql($stSql);
                if ($result) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }

            function montaExcluiCidadao()
            {
                global $sessao;
                $codCidadao = $sessao->transf4[cidadao][codCidadao];
                $sSQL .= " DELETE FROM cse.cidadao_programa WHERE cod_cidadao = ".$codCidadao.";";
                $sSQL .= " DELETE FROM cse.resposta_censo WHERE cod_cidadao = ".$codCidadao.";";
                $sSQL .= " DELETE FROM cse.responsavel WHERE cod_cidadao = ".$codCidadao.";";
                $sSQL .= " DELETE FROM cse.qualificacao_profissional WHERE cod_cidadao = ".$codCidadao.";";
                $sSQL .= " DELETE FROM cse.qualificacao_escolar WHERE cod_cidadao = ".$codCidadao.";";
                $sSQL .= " DELETE FROM cse.cidadao_domicilio WHERE cod_cidadao = ".$codCidadao.";";
                $sSQL .= " DELETE FROM cse.cidadao WHERE cod_cidadao = ".$codCidadao.";";

                return $sSQL;
            }

            function montaIncluiCidadao()
            {
               global $sessao;
               global $_POST;
                foreach ($sessao->transf4 as $tabela => $valor) {
                    $$tabela = $valor;
                }

                $codCidadao = $cidadao[codCidadao];

                $arMoradia = explode("-", $cidadao[codUnidadeMedida]);
                foreach ($arMoradia as $indice => $valor) {
                    if ( strtoupper( $valor ) == "XXX" or empty($valor) ) {
                        $arMoradia[$indice] = "0";
                    }
                }
                foreach ($cidadao as $indice => $valor) {
                    if ( strtoupper( $valor ) == "XXX" ) {
                        $cidadao[$indice] = "0";
                    }
                }
                if ( is_array( $documentacao ) ) {
                    foreach ($documentacao as $indice => $valor) {
                        if ( strtoupper( $valor ) == "XXX" ) {
                            $documentacao[$indice] = "0";
                        }
                    }
                }
                if ( is_array( $vinculo ) ) {
                    foreach ($vinculo as $indice => $valor) {
                        if ( strtoupper( $valor ) == "XXX" ) {
                            $vinculo[$indice] = "0";
                        }
                    }
                }
                if ( empty( $vinculo[amamentando] ) or !isset( $vinculo[amamentando] ) or $vinculo[amamentando] == "f") {
                    $vinculo[amamentando] = "false";
                } else {
                    $vinculo[amamentando] = "true";
                }

                $arMoradia[1]               = $arMoradia[1]                 ? $arMoradia[1]                 : "0";
                $arMoradia[0]               = $arMoradia[0]                 ? $arMoradia[0]                 : "0";
                $cidadao[codDeficiencia]    = $cidadao[codDeficiencia]      ? $cidadao[codDeficiencia]      : "0";
                $cidadao[sexoCidadao]       = $cidadao[sexoCidadao]         ? $cidadao[sexoCidadao]         : "0";
                $cidadao[codRacaCidadao]    = $cidadao[codRacaCidadao]      ? $cidadao[codRacaCidadao]      : "0";
                $cidadao[codEstadoCivil]    = $cidadao[codEstadoCivil]      ? $cidadao[codEstadoCivil]      : "0";
                $documentacao[codCertidao]  = $documentacao[codCertidao]    ? $documentacao[codCertidao]    : "0";
                $vinculo[codGrauParentesco] = $vinculo[codGrauParentesco]   ? $vinculo[codGrauParentesco]   : "0";
                $cidadao[estado]            = $cidadao[estado]              ? $cidadao[estado]              : "0";
                $documentacao[uf]           = $documentacao[uf]             ? $documentacao[uf]             : "0";
                $documentacao[ufRg]         = $documentacao[ufRg]           ? $documentacao[ufRg]           : "0";
                $documentacao[ufCtps]       = $documentacao[ufCtps]         ? $documentacao[ufCtps]         : "0";

                $cidadao[dataNasc]              = $cidadao[dataNasc]               ? "TO_DATE('".$cidadao[dataNasc]."','DD/MM/YYYY')"              : "NULL";
                $documentacao[dataEmissaoCtps]  = $documentacao[dataEmissaoCtps]   ? "TO_DATE('".$documentacao[dataEmissaoCtps]."','DD/MM/YYYY')"  : "NULL";
                $documentacao[dataEmissaoRg]    = $documentacao[dataEmissaoRg]     ? "TO_DATE('".$documentacao[dataEmissaoRg]."','DD/MM/YYYY')"    : "NULL";
                $documentacao[dataEmissao]      = $documentacao[dataEmissao]       ? "TO_DATE('".$documentacao[dataEmissao]."','DD/MM/YYY')"       : "NULL";
                $documentacao[dataEntradaBrasil]= $documentacao[dataEntradaBrasil] ? "TO_DATE('".$documentacao[dataEntradaBrasil]."','DD/MM/YYY')" : "NULL";

                $profissionais[vlrSalario]          = $profissionais[vlrSalario]            ? $profissionais[vlrSalario]            : 0;
                $profissionais[vlrAposentadoria]    = $profissionais[vlrAposentadoria]      ? $profissionais[vlrAposentadoria]      : 0;
                $profissionais[vlrSegdesmprego]     = $profissionais[vlrSegdesmprego]       ? $profissionais[vlrSegdesmprego]       : 0;
                $profissionais[vlrPensaoAlimenticia]= $profissionais[vlrPensaoAlimenticia]  ? $profissionais[vlrPensaoAlimenticia]  : 0;
                $profissionais[vlrOutrasRendas]     = $profissionais[vlrOutrasRendas]       ? $profissionais[vlrOutrasRendas]       : 0;

                $cidadao[tempoMoradia]  = $cidadao[tempoMoradia]    ? $cidadao[tempoMoradia]    : "0";
                $vinculo[mesGestacao]   = $vinculo[mesGestacao]     ? $vinculo[mesGestacao]     : 0;
                $vinculo[qtdFilhos]     = $vinculo[qtdFilhos]       ? $vinculo[qtdFilhos]       : 0;

                $vlrSalario             = str_replace(".","", $profissionais[vlrSalario]);
                $vlrAposentadoria       = str_replace(".","", $profissionais[vlrAposentadoria]);
                $vlrSegdesmprego        = str_replace(".","", $profissionais[vlrSegdesmprego]);
                $vlrPensaoAlimenticia   = str_replace(".","", $profissionais[vlrPensaoAlimenticia]);
                $vlrOutrasRendas        = str_replace(".","", $profissionais[vlrOutrasRendas]);

                $vlrSalario             = str_replace(",",".", $vlrSalario);
                $vlrAposentadoria       = str_replace(",",".", $vlrAposentadoria);
                $vlrSegdesmprego        = str_replace(",",".", $vlrSegdesmprego);
                $vlrPensaoAlimenticia   = str_replace(",",".", $vlrPensaoAlimenticia);
                $vlrOutrasRendas        = str_replace(",",".", $vlrOutrasRendas);

                $vlrSalario             = number_format($vlrSalario,2,".","");
                $vlrAposentadoria       = number_format($vlrAposentadoria,2,".","");
                $vlrSegdesmprego        = number_format($vlrSegdesmprego,2,".","");
                $vlrPensaoAlimenticia   = number_format($vlrPensaoAlimenticia,2,".","");
                $vlrOutrasRendas        = number_format($vlrOutrasRendas,2,".","");

                $stQuebra = "<br>";
                $stSql  = " INSERT INTO cse.cidadao ("                  .$stQuebra;
                $stSql .= "     cod_cidadao, "                          .$stQuebra;
                $stSql .= "     cod_grandeza_moradia, "                 .$stQuebra;
                $stSql .= "     cod_unidade_moradia, "                  .$stQuebra;
                $stSql .= "     cod_deficiencia, "                      .$stQuebra;
                $stSql .= "     cod_sexo, "                             .$stQuebra;
                $stSql .= "     cod_raca, "                             .$stQuebra;
                $stSql .= "     cod_estado_civil, "                     .$stQuebra;
                $stSql .= "     cod_tipo_certidao, "                    .$stQuebra;
                $stSql .= "     cod_grau_parentesco, "                  .$stQuebra;
                $stSql .= "     cod_municipio_origem, "                 .$stQuebra;
                $stSql .= "     cod_uf_origem, "                        .$stQuebra;
                $stSql .= "     cod_uf_certidao, "                      .$stQuebra;
                $stSql .= "     cod_uf_rg, "                            .$stQuebra;
                $stSql .= "     cod_uf_ctps, "                          .$stQuebra;
                $stSql .= "     nom_cidadao, "                          .$stQuebra;
                $stSql .= "     telefone_celular, "                     .$stQuebra;
                $stSql .= "     dt_nascimento, "                        .$stQuebra;
                $stSql .= "     pais_origem, "                          .$stQuebra;
                $stSql .= "     dt_entrada_pais, "                      .$stQuebra;
                $stSql .= "     num_identificacao_social, "             .$stQuebra;
                $stSql .= "     num_termo_certidao, "                   .$stQuebra;
                $stSql .= "     num_livro_certidao, "                   .$stQuebra;
                $stSql .= "     num_folha_certidao, "                   .$stQuebra;
                $stSql .= "     dt_emissao_certidao, "                  .$stQuebra;
                $stSql .= "     nom_cartorio_certidao, "                .$stQuebra;
                $stSql .= "     num_cartao_saude, "                     .$stQuebra;
                $stSql .= "     num_rg, "                               .$stQuebra;
                $stSql .= "     complemento_rg, "                       .$stQuebra;
                $stSql .= "     orgao_emissor_rg, "                     .$stQuebra;
                $stSql .= "     dt_emissao_rg, "                        .$stQuebra;
                $stSql .= "     num_ctps, "                             .$stQuebra;
                $stSql .= "     serie_ctps, "                           .$stQuebra;
                $stSql .= "     dt_emissao_ctps, "                      .$stQuebra;
                $stSql .= "     num_cpf, "                              .$stQuebra;
                $stSql .= "     num_titulo_eleitor, "                   .$stQuebra;
                $stSql .= "     zona_titulo_eleitor, "                  .$stQuebra;
                $stSql .= "     secao_titulo_eleitor, "                 .$stQuebra;
                $stSql .= "     pis_pasep, "                            .$stQuebra;
                $stSql .= "     cbo_r, "                                .$stQuebra;
                $stSql .= "     vl_salario, "                           .$stQuebra;
                $stSql .= "     vl_aposentadoria, "                     .$stQuebra;
                $stSql .= "     vl_seguro_desemprego, "                 .$stQuebra;
                $stSql .= "     vl_pensao_alimenticia, "                .$stQuebra;
                $stSql .= "     vl_outras_rendas, "                     .$stQuebra;
                $stSql .= "     tempo_moradia, "                        .$stQuebra;
                $stSql .= "     pessoa_responsavel, "                   .$stQuebra;
                $stSql .= "     mes_gestacao, "                         .$stQuebra;
                $stSql .= "     amamentando, "                          .$stQuebra;
                $stSql .= "     qtd_filhos, "                           .$stQuebra;
                $stSql .= "     nom_pai, "                              .$stQuebra;
                $stSql .= "     nom_mae "                               .$stQuebra;
                $stSql .= " )VALUES ("                                  .$stQuebra;
                $stSql .= "     '".$codCidadao."', "                    .$stQuebra;
                $stSql .= "     ".$arMoradia[1].", "                    .$stQuebra;
                $stSql .= "     ".$arMoradia[0].", "                    .$stQuebra;
                $stSql .= "     '".$cidadao[codDeficiencia]."', "       .$stQuebra;
                $stSql .= "     '".$cidadao[sexoCidadao]."', "          .$stQuebra;
                $stSql .= "     '".$cidadao[codRacaCidadao]."', "       .$stQuebra;
                $stSql .= "     '".$cidadao[codEstadoCivil]."', "       .$stQuebra;
                $stSql .= "     '".$documentacao[codCertidao]."', "     .$stQuebra;
                $stSql .= "     '".$vinculo[codGrauParentesco]."', "    .$stQuebra;
                $stSql .= "     '".$cidadao[municipio]."', "            .$stQuebra;
                $stSql .= "     '".$cidadao[estado]."', "               .$stQuebra;
                $stSql .= "     '".$documentacao[uf]."', "              .$stQuebra;
                $stSql .= "     '".$documentacao[ufRg]."', "            .$stQuebra;
                $stSql .= "     '".$documentacao[ufCtps]."', "          .$stQuebra;
                $stSql .= "     '".$cidadao[nomCgm]."', "               .$stQuebra;
                $stSql .= "     '".$cidadao[telCelular]."', "           .$stQuebra;
                $stSql .= "     ".$cidadao[dataNasc].", "               .$stQuebra;
                $stSql .= "     '".$cidadao[paisOrCidadao]."', "        .$stQuebra;
                $stSql .= "     ".$documentacao[dataEntradaBrasil].", " .$stQuebra;
                $stSql .= "     '".$documentacao[numIdentSocial]."', "  .$stQuebra;
                $stSql .= "     '".$documentacao[numTermo]."', "        .$stQuebra;
                $stSql .= "     '".$documentacao[numLivro]."', "        .$stQuebra;
                $stSql .= "     '".$documentacao[numFolha]."', "        .$stQuebra;
                $stSql .= "     ".$documentacao[dataEmissao].", "       .$stQuebra;
                $stSql .= "     '".$documentacao[nomCartorio]."', "     .$stQuebra;
                $stSql .= "     '".$documentacao[numCartSaude]."', "    .$stQuebra;
                $stSql .= "     '".$documentacao[rg]."', "              .$stQuebra;
                $stSql .= "     '".$documentacao[complementoRg]."', "   .$stQuebra;
                $stSql .= "     '".$documentacao[orgaoEmissor]."', "    .$stQuebra;
                $stSql .= "     ".$documentacao[dataEmissaoRg].", "     .$stQuebra;
                $stSql .= "     '".$documentacao[numCtps]."', "         .$stQuebra;
                $stSql .= "     '".$documentacao[serieCtps]."', "       .$stQuebra;
                $stSql .= "     ".$documentacao[dataEmissaoCtps].", "   .$stQuebra;
                $stSql .= "     '".$documentacao[cpf]."', "             .$stQuebra;
                $stSql .= "     '".$documentacao[numTitEleitor]."', "   .$stQuebra;
                $stSql .= "     '".$documentacao[zonaEleitor]."', "     .$stQuebra;
                $stSql .= "     '".$documentacao[secaoEleitor]."', "    .$stQuebra;
                $stSql .= "     '".$documentacao[pis]."', "             .$stQuebra;
                $stSql .= "     '".$documentacao[cbor]."', "            .$stQuebra;
                $stSql .= "     ".$vlrSalario.", "                      .$stQuebra;
                $stSql .= "     ".$vlrAposentadoria.", "                .$stQuebra;
                $stSql .= "     ".$vlrSegdesmprego.", "                 .$stQuebra;
                $stSql .= "     ".$vlrPensaoAlimenticia.", "            .$stQuebra;
                $stSql .= "     ".$vlrOutrasRendas.", "                 .$stQuebra;
                $stSql .= "     ".$cidadao[tempoMoradia].", "           .$stQuebra;
                $stSql .= "     '".$vinculo[respCrianca]."', "          .$stQuebra;
                $stSql .= "     ".$vinculo[mesGestacao].", "            .$stQuebra;
                $stSql .= "     ".$vinculo[amamentando].", "            .$stQuebra;
                $stSql .= "     ".$vinculo[qtdFilhos].", "              .$stQuebra;
                $stSql .= "     '".$cidadao[nomPai]."', "               .$stQuebra;
                $stSql .= "     '".$cidadao[nomMae]."'); "              .$stQuebra;

                //$stQuebra = "<br>";
                $stSql .= " INSERT INTO cse.cidadao_domicilio ("    .$stQuebra;
                $stSql .= "     cod_cidadao, "                      .$stQuebra;
                $stSql .= "     cod_domicilio, "                    .$stQuebra;
                $stSql .= "     dt_inclusao "                       .$stQuebra;
                $stSql .= " ) VALUES ( "                            .$stQuebra;
                $stSql .= "     ".$codCidadao.", "                  .$stQuebra;
                $stSql .= "     ".$cidadao[codDomicilio].", "       .$stQuebra;
                $stSql .= "     NOW() ); "                          .$stQuebra;

                $escolaridade[grauInstrucao]    = strtoupper( $escolaridade[grauInstrucao] )    != "XXX"  ? $escolaridade[grauInstrucao]      : "";
                $escolaridade[instEducacional]  = strtoupper( $escolaridade[instEducacional] )  != "XXX"  ? $escolaridade[instEducacional]    : "";

                if ($escolaridade[grauInstrucao] and  $escolaridade[instEducacional]) {
                    //$stQuebra = "<br>";
                    $stSql .= " INSERT INTO cse.qualificacao_escolar ("     .$stQuebra;
                    $stSql .= "     cod_grau, "                             .$stQuebra;
                    $stSql .= "     cod_instituicao, "                      .$stQuebra;
                    $stSql .= "     cod_cidadao, "                          .$stQuebra;
                    $stSql .= "     dt_cadastro, "                          .$stQuebra;
                    $stSql .= "     serie, "                                .$stQuebra;
                    $stSql .= "     frequencia "                            .$stQuebra;
                    $stSql .= " ) VALUES ( "                                .$stQuebra;
                    $stSql .= "     ".$escolaridade[grauInstrucao].", "     .$stQuebra;
                    $stSql .= "     ".$escolaridade[instEducacional].", "   .$stQuebra;
                    $stSql .= "     ".$codCidadao.", "                      .$stQuebra;
                    $stSql .= "     NOW(), "                                .$stQuebra;
                    $stSql .= "    '".$escolaridade[serie]."', "            .$stQuebra;
                    $stSql .= "    '".$escolaridade[frequancia]."'); "      .$stQuebra;
                }

                if ( strtoupper( $profissionais[codProfissao] )  == "XXX"  or empty( $profissionais[codProfissao] ) ) {
                    $profissionais[codProfissao] = "0";
                }
                if ( strtoupper( $profissionais[codEmpresa] )  == "XXX"  or empty( $profissionais[codEmpresa] ) ) {
                    $profissionais[codEmpresa] = "0";
                }
                if ( !isset( $profissionais[empregado] ) or empty( $profissionais[empregado] ) ) {
                    $profissionais[empregado] = "f";
                }
                $profissionais[dataAdmissao] = $profissionais[dataAdmissao] ? "TO_DATE('".$profissionais[dataAdmissao]."', 'DD/MM/YYYY')" : "NULL";
                $stQuebra = "<br>";
                $stSql .= " INSERT INTO cse.qualificacao_profissional ("    .$stQuebra;
                $stSql .= "     cod_profissao, "                            .$stQuebra;
                $stSql .= "     cod_empresa, "                              .$stQuebra;
                $stSql .= "     cod_cidadao, "                              .$stQuebra;
                $stSql .= "     dt_cadastro, "                              .$stQuebra;
                $stSql .= "     dt_admissao, "                              .$stQuebra;
                $stSql .= "     emprego_atual, "                            .$stQuebra;
                $stSql .= "     ocupacao "                                  .$stQuebra;
                $stSql .= " ) VALUES ( "                                    .$stQuebra;
                $stSql .= "     ".$profissionais[codProfissao].", "         .$stQuebra;
                $stSql .= "     ".$profissionais[codEmpresa].", "           .$stQuebra;
                $stSql .= "     ".$codCidadao.", "                          .$stQuebra;
                $stSql .= "     NOW(), "                                    .$stQuebra;
                $stSql .= "     ".$profissionais[dataAdmissao].", "         .$stQuebra;
                $stSql .= "    '".$profissionais[empregado]."', "           .$stQuebra;
                $stSql .= "    '".$profissionais[ocupacao]."'); "           .$stQuebra;

                if ($cidadao[respDomicilio] == "t") {
                    $vlrAluguel         = str_replace(".","", $despesas[vlrAluguel]);
                    $vlrDespCasaPropria = str_replace(".","", $despesas[vlrDespCasaPropria]);
                    $vlrDespAlimentacao = str_replace(".","", $despesas[vlrDespAlimentacao]);
                    $vlrDespAgua        = str_replace(".","", $despesas[vlrDespAgua]);
                    $vlrDespEnergia     = str_replace(".","", $despesas[vlrDespEnergia]);
                    $vlrDespTransporte  = str_replace(".","", $despesas[vlrDespTransporte]);
                    $vlrDespRemedio     = str_replace(".","", $despesas[vlrDespRemedio]);
                    $vlrDespGas         = str_replace(".","", $despesas[vlrDespGas]);
                    $vlrDespDiversas    = str_replace(".","", $despesas[vlrDespDiversas]);

                    $vlrAluguel         = str_replace(",",".", $vlrAluguel);
                    $vlrDespCasaPropria = str_replace(",",".", $vlrDespCasaPropria);
                    $vlrDespAlimentacao = str_replace(",",".", $vlrDespAlimentacao);
                    $vlrDespAgua        = str_replace(",",".", $vlrDespAgua);
                    $vlrDespEnergia     = str_replace(",",".", $vlrDespEnergia);
                    $vlrDespTransporte  = str_replace(",",".", $vlrDespTransporte);
                    $vlrDespRemedio     = str_replace(",",".", $vlrDespRemedio);
                    $vlrDespGas         = str_replace(",",".", $vlrDespGas);
                    $vlrDespDiversas    = str_replace(",",".", $vlrDespDiversas);

                    $vlrAluguel         = number_format($vlrAluguel,2,".","");
                    $vlrDespCasaPropria = number_format($vlrDespCasaPropria,2,".","");
                    $vlrDespAlimentacao = number_format($vlrDespAlimentacao,2,".","");
                    $vlrDespAgua        = number_format($vlrDespAgua,2,".","");
                    $vlrDespEnergia     = number_format($vlrDespEnergia,2,".","");
                    $vlrDespTransporte  = number_format($vlrDespTransporte,2,".","");
                    $vlrDespRemedio     = number_format($vlrDespRemedio,2,".","");
                    $vlrDespGas         = number_format($vlrDespGas,2,".","");
                    $vlrDespDiversas    = number_format($vlrDespDiversas,2,".","");

                    $qtdDependentes = $despesas[qtdDependentes] ? $despesas[qtdDependentes] : 0;

                    $stQuebra = "<br>";
                    $stSql .= " INSERT INTO cse.responsavel ("          .$stQuebra;
                    $stSql .= "     cod_cidadao, "                      .$stQuebra;
                    $stSql .= "     cod_domicilio,"                     .$stQuebra;
                    $stSql .= "     dt_inclusao,"                       .$stQuebra;
                    $stSql .= "     numcgm, "                           .$stQuebra;
                    $stSql .= "     vl_aluguel, "                       .$stQuebra;
                    $stSql .= "     vl_prestacao_habitacional, "        .$stQuebra;
                    $stSql .= "     vl_alimentacao, "                   .$stQuebra;
                    $stSql .= "     vl_agua, "                          .$stQuebra;
                    $stSql .= "     vl_luz, "                           .$stQuebra;
                    $stSql .= "     vl_transporte, "                    .$stQuebra;
                    $stSql .= "     vl_remedio, "                       .$stQuebra;
                    $stSql .= "     vl_gas, "                           .$stQuebra;
                    $stSql .= "     vl_despesas_diversas, "             .$stQuebra;
                    $stSql .= "     num_dependentes "                   .$stQuebra;
                    $stSql .= " ) VALUES ( "                            .$stQuebra;
                    $stSql .= "     ".$codCidadao.", "                  .$stQuebra;
                    $stSql .= "     ".$cidadao[codDomicilio].", "       .$stQuebra;
                    $stSql .= "     NOW(), "                            .$stQuebra;
                    $stSql .= "     ".$_POST[numCgm].", "               .$stQuebra;
                    $stSql .= "     ".$vlrAluguel.", "                  .$stQuebra;
                    $stSql .= "     ".$vlrDespCasaPropria.", "          .$stQuebra;
                    $stSql .= "     ".$vlrDespAlimentacao.", "          .$stQuebra;
                    $stSql .= "     ".$vlrDespAgua.", "                 .$stQuebra;
                    $stSql .= "     ".$vlrDespEnergia.", "              .$stQuebra;
                    $stSql .= "     ".$vlrDespTransporte.", "           .$stQuebra;
                    $stSql .= "     ".$vlrDespRemedio.", "              .$stQuebra;
                    $stSql .= "     ".$vlrDespGas.", "                  .$stQuebra;
                    $stSql .= "     ".$vlrDespDiversas.", "             .$stQuebra;
                    $stSql .= "     ".$qtdDependentes."); "             .$stQuebra;
                }

                if ( is_array($censo) ) {
                    foreach ($censo as $indice => $valor) {
                        $posQuest = strpos( $indice, "questaoCenso" );
                        $posMultiplaResposta = strpos( $indice, "][0]" );
                        if ($posQuest !== false and $posMultiplaResposta === false) {
                            $indice = str_replace("questaoCenso[", "", $indice );
                            $posChave  = strpos( $indice, "]" ) ;
                            $chaveQuestao = trim( substr( $indice, 0, $posChave ) );
                            $arChaveQuestao = explode("_", $chaveQuestao);
                            $anoCenso = $arChaveQuestao[0];
                            $codQuestao = $arChaveQuestao[1];
                            $stSql .= " INSERT INTO cse.resposta_censo ( "                  .$stQuebra;
                            $stSql .= "     cod_questao, "                                  .$stQuebra;
                            $stSql .= "     exercicio, "                                    .$stQuebra;
                            $stSql .= "     cod_cidadao, "                                  .$stQuebra;
                            $stSql .= "     cod_resposta, "                                 .$stQuebra;
                            $stSql .= "     resposta "                                      .$stQuebra;
                            $stSql .= " ) SELECT "                                          .$stQuebra;
                            $stSql .= "     ".$codQuestao." AS codQuestao, "                .$stQuebra;
                            $stSql .= "     '".$anoCenso."' AS ano, "                       .$stQuebra;
                            $stSql .= "     ".$codCidadao." AS codCidadao, "                .$stQuebra;
                            $stSql .= "     (CASE WHEN MAX(cod_resposta) IS NULL THEN 1 ELSE MAX(cod_resposta) + 1 END) AS cod_resposta, ".$stQuebra;
                            $stSql .= "     '".$valor."' AS valor "                         .$stQuebra;
                            $stSql .= "   FROM cse.resposta_censo "                         .$stQuebra;
                            $stSql .= "   WHERE "                                           .$stQuebra;
                            $stSql .= "       cod_questao  = ".$codQuestao." and "          .$stQuebra;
                            $stSql .= "       exercicio    = '".$anoCenso."' and "          .$stQuebra;
                            $stSql .= "       cod_cidadao = ".$codCidadao."; "              .$stQuebra;
                        }
                    }
                }

                if ( is_array($programas) ) {
                    foreach ($programas as $indice => $valor) {
                        $posPS = strpos( $indice, "ps" );

                        if ($posPS !== false) {
                            $indice = str_replace("ps", "", $indice);
                            $indice = str_replace("[", "",$indice);
                            $indice = str_replace("]", "",$indice);
                            $arIndice = preg_split( "/_/", $indice);
                            $codQuestao =  $arIndice[1];
                            $arProgramas[$codQuestao][$arIndice[2]] = $valor;
                            $arProgramas[$codQuestao][ano] = $arIndice[0];
                        }
                    }
                }

                if ( is_array( $arProgramas ) ) {
                    foreach ($arProgramas as $indice => $valor) {
//						if ($arProgramas[$indice][cod] == true AND $arProgramas[$indice][di]) {
                        if ($arProgramas[$indice][di]) {
                            $prioritario = $arProgramas[$indice][bp] ? "true" : "false";

                            $dtInclusao  = $arProgramas[$indice][di] ? "TO_DATE('".$arProgramas[$indice][di]."','DD/MM/YYYY')" : "NULL";
                            $vlBeneficio = str_replace(".","", $arProgramas[$indice][vl]);
                            $vlBeneficio = str_replace(",", ".", $vlBeneficio);
                            $vlBeneficio = number_format($vlBeneficio,2, ".", "");

                            $stSql .= " INSERT INTO cse.cidadao_programa( "         .$stQuebra;
                            $stSql .= "     cod_programa, "                         .$stQuebra;
                            $stSql .= "     exercicio, "                            .$stQuebra;
                            $stSql .= "     cod_cidadao, "                          .$stQuebra;
                            $stSql .= "     dt_inclusao, "                          .$stQuebra;
                            $stSql .= "     vl_beneficio, "                         .$stQuebra;
                            $stSql .= "     prioritario "                           .$stQuebra;
                            $stSql .= " ) VALUES ( "                                .$stQuebra;
                            $stSql .= "     ".$indice.", "                          .$stQuebra;
                            $stSql .= "     '".$arProgramas[$indice][ano]."', "     .$stQuebra;
                            $stSql .= "     ".$codCidadao.", "                      .$stQuebra;
                            $stSql .= "     ".$dtInclusao.", "                      .$stQuebra;
                            $stSql .= "     ".$vlBeneficio.", "                     .$stQuebra;
                            $stSql .= "     ".$prioritario."); "                    .$stQuebra;
                        }
                    }
                }

                echo $stSql;
                $stSql = str_replace("<br>", "", $stSql);

                return $stSql;
            }

    /**************************************************************************
     Inclui uma nova Deficiência
    ***************************************************************************/
    public function incluirDeficiencia($nomDeficiencia)
    {
        $codDeficiencia = pegaID("cod_deficiencia","cse.deficiencia");

        $nomDeficiencia = AddSlashes($nomDeficiencia);

        $sql = "Insert Into cse.deficiencia (cod_deficiencia, nom_deficiencia)
                Values ('".$codDeficiencia."', '".$nomDeficiencia."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function Incluir

    /**************************************************************************
    Altera uma Deficiência
    ***************************************************************************/
    public function alterarDeficiencia($codDeficiencia,$nomDeficiencia)
    {
        $nomDeficiencia = AddSlashes($nomDeficiencia);

        $sql = "Update cse.deficiencia
                Set nom_deficiencia = '".$nomDeficiencia."'
                Where cod_deficiencia = '".$codDeficiencia."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterar

    /**************************************************************************
     Exclui uma Deficiência
    ***************************************************************************/
    public function excluirDeficiencia($codDeficiencia)
    {
        $sql = "Delete From cse.deficiencia
                Where cod_deficiencia = '".$codDeficiencia."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluir

    /**************************************************************************
    Inclui uma nova Empresa
    ***************************************************************************/
    public function incluirEmpresa($var)
    {
        //Grava as chaves do vetor como variáveis
        if (is_array($var)) {
            foreach ($var as $chave=>$valor) {
                $$chave = $valor;
            }
        }

        $codEmpresa = pegaID("cod_empresa","cse.empresa");

        $nomEmpresa = AddSlashes($nomEmpresa);

        $sql = "Insert Into cse.empresa (cod_empresa, nom_empresa, cnpj)
                Values ('".$codEmpresa."', '".$nomEmpresa."', '".$cnpj."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirEmpresa

    /**************************************************************************
    Altera uma Empresa
    ***************************************************************************/
    public function alterarEmpresa($var)
    {
        //Grava as chaves do vetor como variáveis
        if (is_array($var)) {
            foreach ($var as $chave=>$valor) {
                $$chave = $valor;
            }
        }

        $nomEmpresa = AddSlashes($nomEmpresa);

        $sql = "Update cse.empresa
                Set nom_empresa = '".$nomEmpresa."',
                    cnpj = '".$cnpj."'
                Where cod_empresa = '".$codEmpresa."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarEmpresa

    /**************************************************************************
    Exclui uma Empresa
    ***************************************************************************/
    public function excluirEmpresa($codEmpresa)
    {
        $sql = "Delete From cse.empresa
                Where cod_empresa = '".$codEmpresa."' ; ";

        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirEmpresa

    /**************************************************************************
    Inclui um novo Programa Social
    ***************************************************************************/
    public function incluirPrograma($var)
    {
        //Grava as chaves do vetor como variáveis
        if (is_array($var)) {
            foreach ($var as $chave=>$valor) {
                $$chave = $valor;
            }
        }

        $codPrograma = pegaID("cod_programa","cse.programa_social");

        $nomPrograma = AddSlashes($nomPrograma);

        $descricao = AddSlashes($descricao);

        $sql = "Insert Into cse.programa_social (cod_programa, exercicio, nom_programa, descricao)
                Values ('".$codPrograma."', '".$exercicio."', '".$nomPrograma."', '".$descricao."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirPrograma

    /**************************************************************************
    Altera um Programa Social
    ***************************************************************************/
    public function alterarPrograma($var)
    {
        //Grava as chaves do vetor como variáveis
        if (is_array($var)) {
            foreach ($var as $chave=>$valor) {
                $$chave = $valor;
            }
        }

        $nomPrograma = AddSlashes($nomPrograma);

        $descricao = AddSlashes($descricao);

        $sql = "Update cse.programa_social
                Set nom_programa = '".$nomPrograma."',
                    descricao = '".$descricao."'
                Where cod_programa = '".$codPrograma."'
                And exercicio = '".$exercicio."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarPrograma

    /**************************************************************************
    Exclui um Programa Social
    ***************************************************************************/
    public function excluirPrograma($codPrograma,$exercicio)
    {
        $sql = "Delete From cse.programa_social
                Where cod_programa = '".$codPrograma."'
                And exercicio = '".$exercicio."' ; ";

        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluiPrograma

    /**************************************************************************
     Inclui um novo Exame
    ***************************************************************************/
    public function incluirExame($var)
    {
        //Grava as chaves do vetor como variáveis
        foreach ($var as $chave=>$valor) {
            $$chave = $valor;
        }

        $codExame = pegaID("cod_exame","cse.tipo_exame");

        $nomExame = AddSlashes($nomExame);

        $sql = "Insert Into cse.tipo_exame (cod_exame, nom_exame, cod_tratamento, cod_classificacao)
                Values ('".$codExame."', '".$nomExame."', '".$codTipo."', '".$codClassificacao."'); ";

        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirExame

    /**************************************************************************
    Altera um Exame
    ***************************************************************************/
    public function alterarExame($codExame,$nomExame)
    {
        $nomExame = AddSlashes($nomExame);

        $sql = "Update cse.tipo_exame
                Set nom_exame = '".$nomExame."'
                Where cod_exame = '".$codExame."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarExame

    /**************************************************************************
     Exclui um Exame
    ***************************************************************************/
    public function excluirExame($codExame)
    {
        $sql = "Delete From cse.tipo_exame
                Where cod_exame = '".$codExame."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirExame

    /**************************************************************************
    Inclui uma novo Tipo de Tratamento
    ***************************************************************************/
    public function incluirTipoTratamento($var)
    {
        //Grava as chaves do vetor como variáveis
        if (is_array($var)) {
            foreach ($var as $chave=>$valor) {
                $$chave = $valor;
            }
        }

        $codTratamento = pegaID("cod_tratamento","cse.tipo_tratamento");

        $nomTratamento = AddSlashes($nomTratamento);

        $sql = "Insert Into cse.tipo_tratamento (cod_tratamento, cod_classificacao, nom_tratamento)
                Values (".$codTratamento.", ".$codClassificacao.", '".$nomTratamento."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirTipoTratamento

    /**************************************************************************
    Altera um tipo de tratamento
    ***************************************************************************/
    public function alterarTipoTratamento($var)
    {
        //Grava as chaves do vetor como variáveis
        if (is_array($var)) {
            foreach ($var as $chave=>$valor) {
                $$chave = $valor;
            }
        }

        $nomTratamento = AddSlashes($nomTratamento);

        $sql = "Update cse.tipo_tratamento
                Set nom_tratamento = '".$nomTratamento."',
                    cod_classificacao = ".$codClassificacao."
                Where cod_tratamento = '".$codTratamento."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarTipoTratamento

    /**************************************************************************
    Exclui um Tipo de Tratamento
    ***************************************************************************/
    public function excluirTipoTratamento($codTratamento)
    {
        $sql = "Delete From cse.tipo_tratamento
                Where cod_tratamento = ".$codTratamento." ; ";

        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirTipoTratamento

    /**************************************************************************
     Inclui uma nova Instituição de Saúde
    ***************************************************************************/
    public function incluirInstituicaoSaude($nomInstituicao)
    {
        $codInstituicao = pegaID("cod_instituicao","cse.instituicao_saude");

        $nomInstituicao = AddSlashes($nomInstituicao);

        $sql = "Insert Into cse.instituicao_saude (cod_instituicao, nom_instituicao)
                Values ('".$codInstituicao."', '".$nomInstituicao."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirInstituicaoSaude

    /**************************************************************************
    Altera uma Instituição de Saúde
    ***************************************************************************/
    public function alterarInstituicaoSaude($codInstituicao,$nomInstituicao)
    {
        $nomInstituicao = AddSlashes($nomInstituicao);

        $sql = "Update cse.instituicao_saude
                Set nom_instituicao = '".$nomInstituicao."'
                Where cod_instituicao = '".$codInstituicao."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarInstituicaoSaude

    /**************************************************************************
     Exclui uma Instituição de Saúde
    ***************************************************************************/
    public function excluirInstituicaoSaude($codInstituicao)
    {
        $sql = "Delete From cse.instituicao_saude
                Where cod_instituicao = '".$codInstituicao."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirInstituicaoSaude

    /**************************************************************************
     Inclui uma nova Profissão
    ***************************************************************************/
    public function incluirProfissao($nomProfissao)
    {
        $codProfissao = pegaID("cod_profissao","cse.profissao");

        $nomProfissao = AddSlashes($nomProfissao);

        $sql = "Insert Into cse.profissao (cod_profissao, nom_profissao)
                Values ('".$codProfissao."', '".$nomProfissao."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirProfissao

    /**************************************************************************
    Altera uma Profissão
    ***************************************************************************/
    public function alterarProfissao($codProfissao,$nomProfissao)
    {
        $nomProfissao = AddSlashes($nomProfissao);

        $sql = "Update cse.profissao
                Set nom_profissao = '".$nomProfissao."'
                Where cod_profissao = '".$codProfissao."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarProfissao

    /**************************************************************************
     Exclui uma Profissão
    ***************************************************************************/
    public function excluirProfissao($codProfissao)
    {
        $sql = "Delete From cse.profissao
                Where cod_profissao = '".$codProfissao."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirProfissao

    /**************************************************************************
     Inclui uma nova Classificação de Tratamento
    ***************************************************************************/
    public function incluirClassTratamento($nomClassTratamento)
    {
        $codClassTratamento = pegaID("cod_classificacao","cse.classificacao_tratamento");

        $nomClassTratamento = AddSlashes($nomClassTratamento);

        $sql = "Insert Into cse.classificacao_tratamento (cod_classificacao, nom_classificacao)
                Values ('".$codClassTratamento."', '".$nomClassTratamento."'); ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function incluirClassTratamento

    /**************************************************************************
    Altera uma Classificação de Tratamento
    ***************************************************************************/
    public function alterarClassTratamento($codClassTratamento,$nomClassTratamento)
    {
        $nomClassTratamento = AddSlashes($nomClassTratamento);

        $sql = "Update cse.classificacao_tratamento
                Set nom_classificacao = '".$nomClassTratamento."'
                Where cod_classificacao = '".$codClassTratamento."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarClassTratamento

    /**************************************************************************
     Exclui uma Classificação de Tratamento
    ***************************************************************************/
    public function excluirClassTratamento($codClassTratamento)
    {
        $sql = "Delete From cse.classificacao_tratamento
                Where cod_classificacao = '".$codClassTratamento."' ; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirClassTratamento

        /**************************************************************************
     Inclui um novo Tratamento
    ***************************************************************************/
    public function incluirTratamento($var)
    {
        //Grava as chaves do vetor como variáveis
        foreach ($var as $chave=>$valor) {
            $$chave = $valor;
        }

        //Tratamento das variáveis
        $codTratamento = pegaID("cod_prescricao","cse.prescricao");
        $this->codigo = $codTratamento;
        $periodicidade = AddSlashes($periodicidade);
        $descricao = AddSlashes($descricao);
        $dataInicio = dataToSql($dataInicio);
        $dataTermino = dataToSql($dataTermino);

        $stQuebra = "<br>";
        $sql  = " INSERT INTO "                 .$stQuebra;
        $sql .= "   cse.prescricao "            .$stQuebra;
        $sql .= " ("                            .$stQuebra;
        $sql .= "   cod_prescricao, "           .$stQuebra;
        $sql .= "   cod_cidadao, "              .$stQuebra;
        $sql .= "   cod_tipo, "                 .$stQuebra;
        $sql .= "   cod_classificacao, "        .$stQuebra;
        $sql .= "   dt_inicio, "                .$stQuebra;
        $sql .= "   dt_termino, "               .$stQuebra;
        $sql .= "   periodicidade, "            .$stQuebra;
        $sql .= "   descricao "                 .$stQuebra;
        $sql .= " ) VALUES ( "                  .$stQuebra;
        $sql .= "   '".$codTratamento."', "     .$stQuebra;
        $sql .= "   '".$codCidadao."', "        .$stQuebra;
        $sql .= "   '".$codTipo."', "           .$stQuebra;
        $sql .= "   '".$codClassificacao."', "  .$stQuebra;
        $sql .= "   '".$dataInicio."', "        .$stQuebra;
        $sql .= "   '".$dataTermino."', "       .$stQuebra;
        $sql .= "   '".$periodicidade."', "     .$stQuebra;
        $sql .= "   '".$descricao."' "          .$stQuebra;
        $sql .= " ); ";

        //Inclui os exames selecionado para o tratamento
        foreach ($exame as $ex) {
            $dataRealizacao = dataToSql($ex[dataExame]);
            $desc = AddSlashes($ex[descExame]);
            $sql .= " INSERT INTO cse.prescricao_exame "                        .$stQuebra;
            $sql .= "     ( "                                                   .$stQuebra;
            $sql .= "     cod_exame, "                                          .$stQuebra;
            $sql .= "     cod_instituicao, "                                    .$stQuebra;
            $sql .= "     cod_prescricao, "                                     .$stQuebra;
            $sql .= "     cod_cidadao, "                                        .$stQuebra;
            $sql .= "     cod_tipo, "                                           .$stQuebra;
            $sql .= "     cod_classificacao, "                                  .$stQuebra;
            $sql .= "     cod_tipo_exame, "                                     .$stQuebra;
            $sql .= "     dt_realizacao, "                                      .$stQuebra;
            $sql .= "     descricao "                                           .$stQuebra;
            $sql .= "     ) "                                                   .$stQuebra;
            $sql .= "     SELECT "                                              .$stQuebra;
            $sql .= "         (CASE WHEN MAX(cod_exame) IS NULL THEN 1 ELSE MAX(cod_exame) + 1 END) AS cod_exame, ".$stQuebra;
            $sql .= "         '".$ex[codInstExame]."' AS cod_instituicao, "     .$stQuebra;
            $sql .= "         '".$codTratamento."' AS cod_prescricao, "         .$stQuebra;
            $sql .= "         '".$codCidadao."' AS cod_cidadao, "               .$stQuebra;
            $sql .= "         '".$codTipo."' AS cod_tipo, "                     .$stQuebra;
            $sql .= "         '".$codClassificacao."' AS cod_classificacao, "   .$stQuebra;
            $sql .= "         '".$ex[codExame]."' AS cod_tipo_exame, "          .$stQuebra;
            $sql .= "         '".$dataRealizacao."' AS dt_realizacao, "         .$stQuebra;
            $sql .= "         '".$desc."' AS descricao "                        .$stQuebra;
            $sql .= "     FROM "                                                .$stQuebra;
            $sql .= "         cse.prescricao_exame     "                        .$stQuebra;
            $sql .= "     WHERE "                                               .$stQuebra;
            $sql .= "         cod_instituicao = ".$ex[codInstExame]." AND "     .$stQuebra;
            $sql .= "         cod_prescricao = ".$codTratamento." AND "         .$stQuebra;
            $sql .= "         cod_cidadao = ".$codCidadao." AND "               .$stQuebra;
            $sql .= "         cod_tipo = ".$codTipo." AND "                     .$stQuebra;
            $sql .= "         cod_classificacao = ".$codClassificacao." AND "   .$stQuebra;
            $sql .= "         cod_tipo_exame = ".$ex[codExame]."; "             .$stQuebra;
        }

        //Inclui internações para o tratamento
        foreach ($internacao as $int) {
            $dataAlta = dataToSql($int[dataAlta]);
            $dataBaixa = dataToSql($int[dataBaixa]);
            $motivo = AddSlashes($int[motivo]);
            $sql .= " INSERT INTO "                                             .$stQuebra;
            $sql .= "     cse.prescricao_internacao "                       .$stQuebra;
            $sql .= " ( "                                                       .$stQuebra;
            $sql .= "     cod_internacao, "                                     .$stQuebra;
            $sql .= "     cod_instituicao, "                                    .$stQuebra;
            $sql .= "     cod_prescricao, "                                     .$stQuebra;
            $sql .= "     cod_cidadao, "                                        .$stQuebra;
            $sql .= "     cod_tipo, "                                           .$stQuebra;
            $sql .= "     cod_classificacao, "                                  .$stQuebra;
            $sql .= "     dt_alta, "                                            .$stQuebra;
            $sql .= "     dt_baixa, "                                           .$stQuebra;
            $sql .= "     motivo "                                              .$stQuebra;
            $sql .= " ) "                                                       .$stQuebra;
            $sql .= "     SELECT "                                              .$stQuebra;
            $sql .= "         (CASE WHEN MAX(cod_internacao) IS NULL THEN 1 ELSE MAX(cod_internacao) + 1 END) AS cod_internacao, ".$stQuebra;
            $sql .= "         '".$int[codInstituicao]."' AS cod_instituicao, "  .$stQuebra;
            $sql .= "         '".$codTratamento."' AS cod_prescricao, "         .$stQuebra;
            $sql .= "         '".$codCidadao."' AS cod_cidadao, "               .$stQuebra;
            $sql .= "         '".$codTipo."' AS cod_tipo, "                     .$stQuebra;
            $sql .= "         '".$codClassificacao."' AS cod_classificacao, "   .$stQuebra;
            $sql .= "         '".$dataAlta."' AS dt_alta, "                     .$stQuebra;
            $sql .= "         '".$dataBaixa."' AS dt_baixa, "                   .$stQuebra;
            $sql .= "         '".$motivo."' AS motivo "                         .$stQuebra;
            $sql .= "     FROM "                                                .$stQuebra;
            $sql .= "         cse.prescricao_internacao"                    .$stQuebra;
            $sql .= "     WHERE "                                               .$stQuebra;
            $sql .= "         cod_prescricao = ".$codTratamento." AND "         .$stQuebra;
            $sql .= "         cod_instituicao = ".$int[codInstituicao]." AND "  .$stQuebra;
            $sql .= "         cod_tipo = ".$codTipo." AND "                     .$stQuebra;
            $sql .= "         cod_classificacao = ".$codClassificacao." AND "   .$stQuebra;
            $sql .= "         cod_cidadao = ".$codCidadao."; "                  .$stQuebra;
        }
        //echo $sql;
        $sql = str_replace( "<br>", "", $sql );
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;

    }//fim da function incluiTratamento

    /**************************************************************************
     Altera um Tratamento
    ***************************************************************************/
    public function alterarTratamento($var)
    {
        //Grava as chaves do vetor como variáveis
        foreach ($var as $chave=>$valor) {
            $$chave = $valor;
        }

        $sql = "";
        //Exclui todos os registros de exames referentes ao tratamento
        $sql .= "Delete From cse.prescricao_exame
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";
        //Exclui todos os registros de internação referentes ao tratamento
        $sql .= "Delete From cse.prescricao_internacao
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";

        //Tratamento das variáveis
        $periodicidade = AddSlashes($periodicidade);
        $descricao = AddSlashes($descricao);
        $dataInicio = dataToSql($dataInicio);
        $dataTermino = dataToSql($dataTermino);

        //Altera os dados do tratamento
        $sql .= "Update  cse.prescricao Set
                dt_inicio = '".$dataInicio."',
                dt_termino = '".$dataTermino."',
                periodicidade = '".$periodicidade."',
                descricao = '".$descricao."'
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";

        //Inclui os exames selecionados para o tratamento
        foreach ($exame as $ex) {
            $dataRealizacao = dataToSql($ex[dataExame]);
            $desc = AddSlashes($ex[descExame]);
            $sql .= " INSERT INTO cse.prescricao_exame "                    .$stQuebra;
            $sql .= "     ( "                                                   .$stQuebra;
            $sql .= "     cod_exame, "                                          .$stQuebra;
            $sql .= "     cod_instituicao, "                                    .$stQuebra;
            $sql .= "     cod_prescricao, "                                     .$stQuebra;
            $sql .= "     cod_cidadao, "                                        .$stQuebra;
            $sql .= "     cod_tipo, "                                           .$stQuebra;
            $sql .= "     cod_classificacao, "                                  .$stQuebra;
            $sql .= "     cod_tipo_exame, "                                     .$stQuebra;
            $sql .= "     dt_realizacao, "                                      .$stQuebra;
            $sql .= "     descricao "                                           .$stQuebra;
            $sql .= "     ) "                                                   .$stQuebra;
            $sql .= "     SELECT "                                              .$stQuebra;
            $sql .= "         (CASE WHEN MAX(cod_exame) IS NULL THEN 1 ELSE MAX(cod_exame) + 1 END) AS cod_exame, ".$stQuebra;
            $sql .= "         '".$ex[codInstExame]."' AS cod_instituicao, "     .$stQuebra;
            $sql .= "         '".$codTratamento."' AS cod_prescricao, "         .$stQuebra;
            $sql .= "         '".$codCidadao."' AS cod_cidadao, "               .$stQuebra;
            $sql .= "         '".$codTipo."' AS cod_tipo, "                     .$stQuebra;
            $sql .= "         '".$codClassificacao."' AS cod_classificacao, "   .$stQuebra;
            $sql .= "         '".$ex[codExame]."' AS cod_tipo_exame, "          .$stQuebra;
            $sql .= "         '".$dataRealizacao."' AS dt_realizacao, "         .$stQuebra;
            $sql .= "         '".$desc."' AS descricao "                        .$stQuebra;
            $sql .= "     FROM "                                                .$stQuebra;
            $sql .= "         sw_prescricao_exame "                             .$stQuebra;
            $sql .= "     WHERE "                                               .$stQuebra;
            $sql .= "         cod_instituicao = ".$ex[codInstExame]." AND "     .$stQuebra;
            $sql .= "         cod_prescricao = ".$codTratamento." AND "         .$stQuebra;
            $sql .= "         cod_cidadao = ".$codCidadao." AND "               .$stQuebra;
            $sql .= "         cod_tipo = ".$codTipo." AND "                     .$stQuebra;
            $sql .= "         cod_classificacao = ".$codClassificacao." AND "   .$stQuebra;
            $sql .= "         cod_tipo_exame = ".$ex[codExame]."; "             .$stQuebra;
        }

        //Inclui internações para o tratamento
        foreach ($internacao as $int) {
            $dataAlta = dataToSql($int[dataAlta]);
            $dataBaixa = dataToSql($int[dataBaixa]);
            $motivo = AddSlashes($int[motivo]);
            $sql .= " INSERT INTO "                                             .$stQuebra;
            $sql .= "     cse.prescricao_internacao "                           .$stQuebra;
            $sql .= " ( "                                                       .$stQuebra;
            $sql .= "     cod_internacao, "                                     .$stQuebra;
            $sql .= "     cod_instituicao, "                                    .$stQuebra;
            $sql .= "     cod_prescricao, "                                     .$stQuebra;
            $sql .= "     cod_cidadao, "                                        .$stQuebra;
            $sql .= "     cod_tipo, "                                           .$stQuebra;
            $sql .= "     cod_classificacao, "                                  .$stQuebra;
            $sql .= "     dt_alta, "                                            .$stQuebra;
            $sql .= "     dt_baixa, "                                           .$stQuebra;
            $sql .= "     motivo "                                              .$stQuebra;
            $sql .= " ) "                                                       .$stQuebra;
            $sql .= "     SELECT "                                              .$stQuebra;
            $sql .= "         (CASE WHEN MAX(cod_internacao) IS NULL THEN 1 ELSE MAX(cod_internacao) + 1 END) AS cod_internacao, ".$stQuebra;
            $sql .= "         '".$int[codInstituicao]."' AS cod_instituicao, "  .$stQuebra;
            $sql .= "         '".$codTratamento."' AS cod_prescricao, "         .$stQuebra;
            $sql .= "         '".$codCidadao."' AS cod_cidadao, "               .$stQuebra;
            $sql .= "         '".$codTipo."' AS cod_tipo, "                     .$stQuebra;
            $sql .= "         '".$codClassificacao."' AS cod_classificacao, "   .$stQuebra;
            $sql .= "         '".$dataAlta."' AS dt_alta, "                     .$stQuebra;
            $sql .= "         '".$dataBaixa."' AS dt_baixa, "                   .$stQuebra;
            $sql .= "         '".$motivo."' AS motivo "                         .$stQuebra;
            $sql .= "     FROM "                                                .$stQuebra;
            $sql .= "         sw_prescricao_internacao "                        .$stQuebra;
            $sql .= "     WHERE "                                               .$stQuebra;
            $sql .= "         cod_prescricao = ".$codTratamento." AND "         .$stQuebra;
            $sql .= "         cod_instituicao = ".$int[codInstituicao]." AND "  .$stQuebra;
            $sql .= "         cod_tipo = ".$codTipo." AND "                     .$stQuebra;
            $sql .= "         cod_classificacao = ".$codClassificacao." AND "   .$stQuebra;
            $sql .= "         cod_cidadao = ".$codCidadao."; "                  .$stQuebra;
        }

        //echo $sql;
        $sql = str_replace( "<br>", "", $sql );
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;

    }//fim da function alterarTratamento

    /**************************************************************************
     Exclui um Tratamento
    ***************************************************************************/
    public function excluirTratamento($var)
    {
        //Grava as chaves do vetor como variáveis
        foreach ($var as $chave=>$valor) {
            $$chave = $valor;
        }

        $sql = "";
        //Exclui todos os registros de exames referentes ao tratamento
        $sql .= "Delete From cse.prescricao_exame
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";
        //Exclui todos os registros de internação referentes ao tratamento
        $sql .= "Delete From cse.prescricao_internacao
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";

        //Exclui o tratamento
        $sql .= "Delete From cse.prescricao
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";

        //echo $sql;
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;

    }//fim da function excluirTratamento

    /**************************************************************************
     Retorna todos os dados de um Tratamento
    ***************************************************************************/
    public function retornaTratamento($var)
    {
        //Grava as chaves do vetor como variáveis
        foreach ($var as $chave=>$valor) {
            $$chave = $valor;
        }

        //Inicializa variável de retorno
        $vet = array();
        $vet[cse] = array();
        $vet[exame] = array();
        $vet[internacao] = array();

        //Carrega as variáveis fornecidas
        $vet[cse][codTratamento] = $codTratamento;
        $vet[cse][codCidadao] = $codCidadao;
        $vet[cse][codClassificacao] = $codClassificacao;
        $vet[cse][codTipo] = $codTipo;

        //Pega os dados do Tratamento
        $sql = "Select dt_inicio, dt_termino, periodicidade, descricao
                From cse.prescricao
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";

        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if (!$conn->eof()) {
                $dataInicio = $conn->pegaCampo("dt_inicio");
                $vet[cse][dataInicio] = dataToBr($dataInicio);
                $dataTermino = $conn->pegaCampo("dt_termino");
                $vet[cse][dataTermino] = dataToBr($dataTermino);
                $vet[cse][periodicidade] = $conn->pegaCampo("periodicidade");
                $vet[cse][descricao] = $conn->pegaCampo("descricao");
            }
        $conn->limpaSelecao();

        //Pega a relação de Exames
        $sql = "Select cod_instituicao, cod_exame, dt_realizacao, descricao, cod_tipo_exame
                From cse.prescricao_exame
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            while (!$conn->eof()) {
                $aux = array();
                $aux[descExame] = $conn->pegaCampo("descricao");
                $dataExame = $conn->pegaCampo("dt_realizacao");
                $aux[dataExame] = dataToBr($dataExame);
                $aux[codInstExame] = $conn->pegaCampo("cod_instituicao");
                $aux[codExame] = $conn->pegaCampo("cod_tipo_exame");
                $vet[exame][] = $aux;
                $conn->vaiProximo();
            }
        $conn->limpaSelecao();

        //Pega a relação de Internações
        $sql = "Select cod_instituicao, dt_alta, dt_baixa, motivo
                From cse.prescricao_internacao
                Where cod_prescricao = '".$codTratamento."'
                And cod_cidadao = '".$codCidadao."'
                And cod_classificacao = '".$codClassificacao."'
                And cod_tipo = '".$codTipo."'; ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            while (!$conn->eof()) {
                $aux = array();
                $dataBaixa = $conn->pegaCampo("dt_baixa");
                $aux[dataBaixa] = dataToBr($dataBaixa);
                $dataAlta = $conn->pegaCampo("dt_alta");
                $aux[dataAlta] = dataToBr($dataAlta);
                $aux[codInstituicao] = $conn->pegaCampo("cod_instituicao");
                $aux[motivo] = $conn->pegaCampo("motivo");
                $vet[internacao][] = $aux;
                $conn->vaiProximo();
            }
        $conn->limpaSelecao();

        return $vet;

    }//Fim da function retornaTratamento

    }//Fim da Classe
?>
